<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';
    public bool $loading = false;
    public bool $emailSent = false;

    protected $rules = [
        'email' => 'required|email',
    ];

    protected $messages = [
        'email.required' => 'Email is required',
        'email.email' => 'Please enter a valid email address',
    ];

    public function sendResetLink(AuthService $authService)
    {
        $this->loading = true;
        $this->emailSent = false;
        
        $this->validate();

        try {
            $message = $authService->sendPasswordResetLink(
                $this->email,
                request()->ip()
            );

            $this->emailSent = true;
            $this->loading = false;

            $this->dispatch('toast', [
                'message' => $message,
                'variant' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->loading = false;
            
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);

            $this->addError('email', 'Unable to send reset link. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')
            ->layout('components.layouts.guest');
    }
}
