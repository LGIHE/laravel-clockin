<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\AssignSupervisorRequest;
use App\Http\Requests\AssignProjectsRequest;
use App\Http\Requests\ChangeStatusRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
                'department_id' => $request->input('department_id'),
                'user_level_id' => $request->input('user_level_id'),
                'sort_by' => $request->input('sort_by', 'created_at'),
                'sort_direction' => $request->input('sort_direction', 'desc'),
                'per_page' => $request->input('per_page', 10),
            ];

            $users = $this->userService->getUsers($filters);

            return response()->json([
                'success' => true,
                'data' => UserResource::collection($users),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching users',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created user.
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => new UserResource($user),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified user.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);

            return response()->json([
                'success' => true,
                'data' => new UserResource($user),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified user.
     *
     * @param UpdateUserRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        try {
            $user = $this->userService->updateUser($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => new UserResource($user),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified user.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assign a supervisor to a user.
     *
     * @param AssignSupervisorRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function assignSupervisor(AssignSupervisorRequest $request, string $id): JsonResponse
    {
        try {
            $user = $this->userService->assignSupervisor($id, $request->input('supervisor_id'));

            return response()->json([
                'success' => true,
                'message' => 'Supervisor assigned successfully',
                'data' => new UserResource($user),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User or supervisor not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while assigning supervisor',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assign projects to a user.
     *
     * @param AssignProjectsRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function assignProjects(AssignProjectsRequest $request, string $id): JsonResponse
    {
        try {
            $user = $this->userService->assignProjects($id, $request->input('project_ids'));

            return response()->json([
                'success' => true,
                'message' => 'Projects assigned successfully',
                'data' => new UserResource($user),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while assigning projects',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change user status.
     *
     * @param ChangeStatusRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function updateStatus(ChangeStatusRequest $request, string $id): JsonResponse
    {
        try {
            $user = $this->userService->changeStatus($id, $request->input('status'));

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully',
                'data' => new UserResource($user),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change user password.
     *
     * @param ChangePasswordRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request, string $id): JsonResponse
    {
        try {
            $this->userService->changePassword(
                $id,
                $request->input('old_password'),
                $request->input('new_password')
            );

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

