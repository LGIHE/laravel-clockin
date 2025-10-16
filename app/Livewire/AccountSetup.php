<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class AccountSetup extends Component
{
    public $token;
    public $user;
    public $password;
    public $password_confirmation;
    public $tokenValid = false;
    public $tokenExpired = false;
    public $setupComplete = false;

    public function mount($token)
    {
        $this->token = $token;
        
        // Find user with this token
        $this->user = User::where('setup_token', $token)
            ->whereNotNull('setup_token')
            ->first();

        if (!$this->user) {
            $this->tokenValid = false;
            return;
        }

        // Check if token is expired (24 hours)
        if ($this->user->setup_token_expires_at && 
            now()->gt($this->user->setup_token_expires_at)) {
            $this->tokenExpired = true;
            $this->tokenValid = false;
            return;
        }

        $this->tokenValid = true;
    }

    public function setupPassword()
    {
        $this->validate([
            'password' => 'required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|confirmed',
        ], [
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        // Update user password
        $this->user->password = Hash::make($this->password);
        $this->user->setup_token = null;
        $this->user->setup_token_expires_at = null;
        $this->user->password_change_required = false;
        $this->user->save();

        // Log the user in
        Auth::login($this->user);

        // Mark setup as complete to show welcome dialog
        $this->setupComplete = true;
    }

    public function redirectToDashboard()
    {
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.account-setup')->layout('components.layouts.guest');
    }
}

