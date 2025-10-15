<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class FirstLoginPasswordChange extends Component
{
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';
    
    protected function rules()
    {
        return [
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }
    
    protected $messages = [
        'new_password.required' => 'Please enter a new password.',
        'new_password.confirmed' => 'The password confirmation does not match.',
        'new_password.min' => 'Password must be at least 8 characters.',
        'current_password.required' => 'Please enter your current password.',
    ];
    
    public function changePassword()
    {
        $this->validate();
        
        $user = Auth::user();
        
        // Verify current password
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }
        
        // Update password and remove the password change requirement
        $user->update([
            'password' => Hash::make($this->new_password),
            'password_change_required' => false,
        ]);
        
        // Clear the form
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        
        // Show success message and redirect to dashboard
        session()->flash('success', 'Your password has been changed successfully! You can now access your account.');
        
        // Redirect to dashboard after a short delay
        return redirect()->route('dashboard');
    }
    
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
    
    public function render()
    {
        return view('livewire.first-login-password-change')
            ->layout('layouts.guest');
    }
}
