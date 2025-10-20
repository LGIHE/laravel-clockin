<?php

namespace App\Services;

use App\Models\User;
use App\Mail\NewUserAccountMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     * @throws \Exception
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();
        
        try {
            // Admin role protection: Only admins can create admin users
            $userLevelId = $data['user_level_id'];
            if (auth()->check() && auth()->user()->role !== 'ADMIN') {
                $selectedLevel = \App\Models\UserLevel::find($userLevelId);
                if ($selectedLevel && strtoupper($selectedLevel->name) === 'ADMIN') {
                    // Default to User role if non-admin tries to create admin
                    $userRole = \App\Models\UserLevel::where('name', 'User')->first();
                    $userLevelId = $userRole ? $userRole->id : $userLevelId;
                    
                    \Log::warning('Non-admin attempted to create admin user, defaulted to User role', [
                        'actor' => auth()->user()->id,
                        'attempted_role' => $selectedLevel->name
                    ]);
                }
            }
            
            // Generate unique setup token
            $setupToken = Str::random(64);
            $setupTokenExpiresAt = now()->addHours(24);
            
            $userData = [
                'id' => Str::uuid()->toString(),
                'name' => $data['name'],
                'email' => $data['email'],
                'gender' => $data['gender'],
                'phone' => $data['phone'] ?? null,
                'employee_code' => $data['employee_code'] ?? null,
                'password' => Hash::make(Str::random(32)), // Random password, user will set their own
                'user_level_id' => $userLevelId,
                'designation_id' => $data['designation_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'status' => $data['status'] ?? 1,
                'setup_token' => $setupToken,
                'setup_token_expires_at' => $setupTokenExpiresAt,
                'password_change_required' => false, // Not needed since they'll set password via token
            ];

            // Handle project assignment if provided
            if (isset($data['project_ids']) && is_array($data['project_ids'])) {
                $userData['project_id'] = json_encode($data['project_ids']);
            }

            $user = User::create($userData);

            \Log::info('User created in database, preparing to send email', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ]);

            // Generate setup URL
            $setupUrl = url('/account-setup/' . $setupToken);

            // Send account setup email
            \Log::info('Attempting to send account setup email to new user', [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'setup_url' => $setupUrl,
                'mail_mailer' => config('mail.default'),
                'from_address' => config('mail.from.address')
            ]);
            
            try {
                Mail::to($user->email)->send(
                    new NewUserAccountMail($user->name, $user->email, $setupUrl)
                );
                
                \Log::info('Account setup email sent successfully', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            } catch (\Exception $e) {
                // Log the detailed error
                \Log::error('Failed to send account setup email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Re-throw to make it visible
                // throw $e;
            }

            // Invalidate relevant caches
            Cache::forget('admin_system_stats');
            if (isset($userData['department_id'])) {
                Cache::forget("department_users:{$userData['department_id']}");
            }

            DB::commit();

            return $user->load(['userLevel', 'department', 'designation']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing user.
     *
     * @param string $userId
     * @param array $data
     * @return User
     * @throws \Exception
     */
    public function updateUser(string $userId, array $data): User
    {
        DB::beginTransaction();
        
        try {
            $user = User::findOrFail($userId);

            $updateData = [];

            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }

            if (isset($data['email'])) {
                $updateData['email'] = $data['email'];
            }

            if (isset($data['gender'])) {
                $updateData['gender'] = $data['gender'];
            }

            if (isset($data['phone'])) {
                $updateData['phone'] = $data['phone'];
            }

            if (isset($data['employee_code'])) {
                $updateData['employee_code'] = $data['employee_code'];
            }

            if (isset($data['user_level_id'])) {
                // Admin role protection
                $userLevelId = $data['user_level_id'];
                
                if (auth()->check() && auth()->user()->role !== 'ADMIN') {
                    // Check if user is currently admin
                    if ($user->userLevel && strtoupper($user->userLevel->name) === 'ADMIN') {
                        // Non-admin cannot change admin user's role - keep it as is
                        \Log::warning('Non-admin attempted to change admin user role, skipped', [
                            'actor' => auth()->user()->id,
                            'target' => $userId,
                            'current_role' => $user->userLevel->name
                        ]);
                        // Don't update the role
                    } else {
                        // Check if trying to assign admin role
                        $selectedLevel = \App\Models\UserLevel::find($userLevelId);
                        if ($selectedLevel && strtoupper($selectedLevel->name) === 'ADMIN') {
                            // Default to User role
                            $userRole = \App\Models\UserLevel::where('name', 'User')->first();
                            $userLevelId = $userRole ? $userRole->id : $user->user_level_id;
                            
                            \Log::warning('Non-admin attempted to assign admin role, defaulted to User', [
                                'actor' => auth()->user()->id,
                                'target' => $userId
                            ]);
                        }
                        $updateData['user_level_id'] = $userLevelId;
                    }
                } else {
                    // Admin can change any role
                    $updateData['user_level_id'] = $userLevelId;
                }
            }

            if (isset($data['designation_id'])) {
                $updateData['designation_id'] = $data['designation_id'];
            }

            if (isset($data['department_id'])) {
                $updateData['department_id'] = $data['department_id'];
            }

            if (isset($data['status'])) {
                $updateData['status'] = $data['status'];
            }

            // Handle project assignment if provided
            if (isset($data['project_ids'])) {
                if (is_array($data['project_ids'])) {
                    $updateData['project_id'] = json_encode($data['project_ids']);
                } else {
                    $updateData['project_id'] = null;
                }
            }

            $user->update($updateData);

            // Invalidate user cache
            Cache::forget("user:{$userId}");
            Cache::forget('admin_system_stats');
            
            // Invalidate supervisor team caches
            foreach ($user->supervisors as $supervisor) {
                Cache::forget("supervisor_team:{$supervisor->id}");
            }

            DB::commit();

            return $user->fresh(['userLevel', 'department', 'designation']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Assign supervisors to a user.
     *
     * @param string $userId
     * @param array $supervisorData - Can be array of IDs (legacy) or ['primary' => id, 'secondary' => id]
     * @return User
     * @throws \Exception
     */
    public function assignSupervisor(string $userId, array $supervisorData = []): User
    {
        $user = User::findOrFail($userId);

        // Handle both old format (array of IDs) and new format (with types)
        $syncData = [];
        
        if (isset($supervisorData['primary']) || isset($supervisorData['secondary'])) {
            // New format: ['primary' => id, 'secondary' => id]
            if (!empty($supervisorData['primary']) && $supervisorData['primary'] !== $userId) {
                $syncData[$supervisorData['primary']] = ['supervisor_type' => 'primary'];
            }
            if (!empty($supervisorData['secondary']) && $supervisorData['secondary'] !== $userId) {
                $syncData[$supervisorData['secondary']] = ['supervisor_type' => 'secondary'];
            }
        } else {
            // Legacy format: array of supervisor IDs (treat first as primary, second as secondary)
            $supervisorIds = array_filter($supervisorData, function($id) use ($userId) {
                return !empty($id) && $id !== $userId;
            });
            
            $supervisorIds = array_values($supervisorIds); // Re-index array
            
            if (!empty($supervisorIds[0])) {
                $syncData[$supervisorIds[0]] = ['supervisor_type' => 'primary'];
            }
            if (!empty($supervisorIds[1])) {
                $syncData[$supervisorIds[1]] = ['supervisor_type' => 'secondary'];
            }
        }

        // Verify all supervisors exist
        if (!empty($syncData)) {
            $supervisorIds = array_keys($syncData);
            $existingCount = User::whereIn('id', $supervisorIds)->count();
            if ($existingCount !== count($supervisorIds)) {
                throw new \Exception('One or more supervisor IDs are invalid');
            }
        }

        // Sync supervisors with their types
        $user->supervisors()->sync($syncData);

        // Invalidate caches
        Cache::forget("user:{$userId}");
        foreach (array_keys($syncData) as $supervisorId) {
            Cache::forget("supervisor_team:{$supervisorId}");
        }

        return $user->fresh(['userLevel', 'department', 'designation', 'supervisors', 'primarySupervisor', 'secondarySupervisor']);
    }

    /**
     * Assign projects to a user.
     *
     * @param string $userId
     * @param array $projectIds
     * @return User
     * @throws \Exception
     */
    public function assignProjects(string $userId, array $projectIds): User
    {
        $user = User::findOrFail($userId);

        // Store project IDs as JSON
        $user->update([
            'project_id' => !empty($projectIds) ? json_encode($projectIds) : null
        ]);

        return $user->fresh(['userLevel', 'department', 'designation']);
    }

    /**
     * Change user status.
     *
     * @param string $userId
     * @param int $status
     * @return User
     * @throws \Exception
     */
    public function changeStatus(string $userId, int $status): User
    {
        $user = User::findOrFail($userId);

        $user->update(['status' => $status]);

        // Invalidate caches
        Cache::forget("user:{$userId}");
        Cache::forget('admin_system_stats');
        foreach ($user->supervisors as $supervisor) {
            Cache::forget("supervisor_team:{$supervisor->id}");
        }

        return $user->fresh(['userLevel', 'department', 'designation']);
    }

    /**
     * Change user password.
     *
     * @param string $userId
     * @param string $oldPassword
     * @param string $newPassword
     * @return User
     * @throws \Exception
     */
    public function changePassword(string $userId, string $oldPassword, string $newPassword): User
    {
        $user = User::findOrFail($userId);

        // Verify old password
        if (!Hash::check($oldPassword, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return $user;
    }

    /**
     * Get user by ID with relationships.
     *
     * @param string $userId
     * @return User
     */
    public function getUserById(string $userId): User
    {
        return User::with(['userLevel', 'department', 'designation'])
            ->findOrFail($userId);
    }

    /**
     * Get all users with optional filters.
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUsers(array $filters = [])
    {
        $query = User::with(['userLevel', 'department', 'designation']);

        // Apply filters
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (isset($filters['user_level_id'])) {
            $query->where('user_level_id', $filters['user_level_id']);
        }

        // Sorting
        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $filters['per_page'] ?? 10;
        
        return $query->paginate($perPage);
    }

    /**
     * Archive a user instead of deleting.
     * Archived users are hidden from normal views but data is preserved.
     *
     * @param string $userId
     * @return bool
     * @throws \Exception
     */
    public function deleteUser(string $userId): bool
    {
        $user = User::findOrFail($userId);
        
        // Archive the user instead of deleting
        $user->archive();
        $result = true;

        // Invalidate caches
        Cache::forget("user:{$userId}");
        Cache::forget('admin_system_stats');
        foreach ($user->supervisors as $supervisor) {
            Cache::forget("supervisor_team:{$supervisor->id}");
        }
        
        return $result;
    }

    /**
     * Unarchive a user (restore from archive).
     *
     * @param string $userId
     * @return bool
     * @throws \Exception
     */
    public function unarchiveUser(string $userId): bool
    {
        $user = User::findOrFail($userId);
        
        $user->unarchive();

        // Invalidate caches
        Cache::forget("user:{$userId}");
        Cache::forget('admin_system_stats');
        
        return true;
    }

    /**
     * Permanently delete a user and all associated records.
     * This action cannot be undone.
     *
     * @param string $userId
     * @return bool
     * @throws \Exception
     */
    public function permanentDeleteUser(string $userId): bool
    {
        $user = User::findOrFail($userId);
        
        // Delete all related records
        // 1. Delete attendances
        $user->attendances()->delete();
        
        // 2. Delete leaves
        $user->leaves()->delete();
        
        // 3. Detach from projects (many-to-many)
        $user->projects()->detach();
        
        // 4. Remove as supervisor from other users (pivot table)
        \DB::table('user_supervisor')->where('supervisor_id', $userId)->delete();
        
        // 5. Remove this user from being supervised by others (pivot table)
        \DB::table('user_supervisor')->where('user_id', $userId)->delete();
        
        // 6. Delete the user
        $user->forceDelete();

        // Invalidate all related caches
        Cache::forget("user:{$userId}");
        Cache::forget('admin_system_stats');
        Cache::forget("supervisor_team:{$userId}");
        
        // Clear cache for users who had this person as supervisor
        foreach ($user->supervisors as $supervisor) {
            Cache::forget("supervisor_team:{$supervisor->id}");
        }
        
        return true;
    }
}

