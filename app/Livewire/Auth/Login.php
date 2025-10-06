<?php

namespace App\Livewire\Auth;

use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public bool $loading = false;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ];

    protected $messages = [
        'email.required' => 'Email is required',
        'email.email' => 'Please enter a valid email address',
        'password.required' => 'Password is required',
        'password.min' => 'Password must be at least 6 characters',
    ];

    public function mount()
    {
        // Redirect if already authenticated
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
    }

    public function login(AuthService $authService)
    {
        $this->loading = true;
        
        $this->validate();

        try {
            $result = $authService->login(
                $this->email,
                $this->password,
                request()->ip(),
                request()->userAgent()
            );

            // Store token in session for web authentication
            session(['auth_token' => $result['token']]);
            
            // Log the user in
            Auth::loginUsingId($result['user']['id'], $this->remember);

            $this->dispatch('toast', [
                'message' => 'Login successful! Redirecting...',
                'variant' => 'success'
            ]);

            // Redirect based on user role
            $role = strtolower($result['user']['role']);
            
            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            $this->loading = false;
            
            $this->dispatch('toast', [
                'message' => $e->getMessage(),
                'variant' => 'danger'
            ]);

            $this->addError('email', 'Invalid credentials or account inactive');
        }
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.guest');
    }
}
