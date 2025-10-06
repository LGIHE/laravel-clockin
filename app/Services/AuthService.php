<?php

namespace App\Services;

use App\Models\User;
use App\Traits\LogsSecurityEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;

class AuthService
{
    use LogsSecurityEvents;
    /**
     * Authenticate user and generate token.
     *
     * @param string $email
     * @param string $password
     * @param string|null $ip
     * @param string|null $userAgent
     * @return array
     * @throws ValidationException
     */
    public function login(string $email, string $password, ?string $ip = null, ?string $userAgent = null): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            // Log failed login attempt
            $this->logFailedLogin($email, $ip ?? request()->ip(), $userAgent ?? request()->userAgent());
            
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user is active
        if ($user->status !== 1) {
            // Log failed login attempt for inactive account
            $this->logFailedLogin($email, $ip ?? request()->ip(), $userAgent ?? request()->userAgent());
            
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive. Please contact the administrator.'],
            ]);
        }

        // Log successful login
        $this->logSuccessfulLogin($user->id, $email, $ip ?? request()->ip(), $userAgent ?? request()->userAgent());

        // Revoke all existing tokens for this user
        $user->tokens()->delete();

        // Generate new token
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user->load(['userLevel', 'department', 'designation']),
            'token' => $token,
        ];
    }

    /**
     * Logout user by revoking tokens.
     *
     * @param User $user
     * @param string|null $ip
     * @return void
     */
    public function logout(User $user, ?string $ip = null): void
    {
        // Log logout event
        $this->logLogout($user->id, $ip ?? request()->ip());
        
        $user->tokens()->delete();
    }

    /**
     * Refresh user token.
     *
     * @param User $user
     * @return string
     */
    public function refresh(User $user): string
    {
        // Revoke current token
        $user->currentAccessToken()->delete();

        // Generate new token
        return $user->createToken('auth-token')->plainTextToken;
    }

    /**
     * Send password reset link.
     *
     * @param string $email
     * @param string|null $ip
     * @return string
     */
    public function sendPasswordResetLink(string $email, ?string $ip = null): string
    {
        // Log password reset request
        $this->logPasswordResetRequest($email, $ip ?? request()->ip());
        
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }

    /**
     * Reset user password.
     *
     * @param array $credentials
     * @param string|null $ip
     * @return string
     */
    public function resetPassword(array $credentials, ?string $ip = null): string
    {
        $status = Password::reset(
            $credentials,
            function (User $user, string $password) use ($ip) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                // Log password change
                $this->logPasswordChange($user->id, $ip ?? request()->ip());

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return __($status);
    }

    /**
     * Get authenticated user with relationships.
     *
     * @param User $user
     * @return User
     */
    public function getAuthenticatedUser(User $user): User
    {
        return $user->load(['userLevel', 'department', 'designation']);
    }
}
