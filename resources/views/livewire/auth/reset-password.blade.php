<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Create new password</h2>
        <p class="mt-2 text-sm text-gray-600">
            Please enter your new password below.
        </p>
    </div>

    <form wire:submit.prevent="resetPassword">
        
        <!-- Email Field -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email Address
            </label>
            <x-ui.input 
                type="email" 
                id="email"
                wire:model="email"
                placeholder="you@example.com"
                :error="$errors->has('email')"
                aria-label="Email Address"
                aria-required="true"
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                autocomplete="email"
                autofocus
            />
            @error('email')
                <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password Field -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                New Password
            </label>
            <x-ui.input 
                type="password"
                id="password"
                wire:model="password"
                placeholder="Enter new password"
                :error="$errors->has('password')"
                aria-label="New Password"
                aria-required="true"
                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                autocomplete="new-password"
            />
            @error('password')
                <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password Field -->
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                Confirm New Password
            </label>
            <x-ui.input 
                type="password"
                id="password_confirmation"
                wire:model="password_confirmation"
                placeholder="Confirm new password"
                :error="$errors->has('password_confirmation')"
                aria-label="Confirm New Password"
                aria-required="true"
                aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}"
                autocomplete="new-password"
            />
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="mb-4">
            <x-ui.button 
                type="submit" 
                variant="primary" 
                class="w-full"
                :disabled="$loading"
                aria-label="Reset password"
            >
                <span wire:loading.remove wire:target="resetPassword">
                    Reset Password
                </span>
                <span wire:loading wire:target="resetPassword" class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Resetting...
                </span>
            </x-ui.button>
        </div>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('login') }}" 
               class="text-sm font-medium text-blue-600 hover:text-blue-500 focus:outline-none focus:underline transition"
               wire:navigate>
                <span class="inline-flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to login
                </span>
            </a>
        </div>
    </form>
</div>
