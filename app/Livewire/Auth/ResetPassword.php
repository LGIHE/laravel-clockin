<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $loading = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6|confirmed',
        'password_confirmation' => 'required',
    ];

    protected $messages = [
        'email.required' => 'Email is required',
        'email.email' => 'Please enter a valid email address',
        'password.required' => 'Password is required',
        'password.min' => 'Password must be at least 6 characters',
        'password.confirmed' => 'Passwords do not match',
        'password_confirmation.required' => 'Please confirm your password',
    ];

    public function mount($token)
    {
        $this->token = $token;
        $this->email = request()->query('email', '');
    }

    public function resetPassword(AuthService $authService)
    {
        $this->loading = true;
        
        $this->validate();

        try {
            $message = $authService->resetPassword(
                $this->token,
                $this->email,
                $this->password,
                request()->ip()
            );

            $this->dispatch('toast', [
                'message' => $message,
                'variant' => 'success'
            ]);

            // Redirect to login after successful reset
            return redirect()->route('login')->with('success', 'Password reset successfully. Please login with your new password.');

        } catch (\Exception $e) {
            $this->loading = false;
            
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);

            $this->addError('email', 'Unable to reset password. The link may be invalid or expired.');
        }
    }

    public function render()
    {
        return view('livewire.auth.reset-password')
            ->layout('components.layouts.guest');
    }
}
