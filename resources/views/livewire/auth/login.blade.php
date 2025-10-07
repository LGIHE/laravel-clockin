<div>
    <!-- Logo and Title -->
    <div class="flex flex-col items-center mb-8">
        <div class="flex items-center gap-2 mb-4">
            <div class="bg-lgf-blue text-white font-bold p-2 rounded">
                <span class="text-lg">LGF</span>
            </div>
            <div class="text-gray-700 font-medium leading-tight">
                <div class="text-xs uppercase">Luigi</div>
                <div class="text-xs uppercase">Giussani Foundation</div>
            </div>
        </div>
        <h1 class="text-2xl font-semibold text-gray-900 mt-2">LGF Clockin</h1>
        <p class="text-gray-500 text-sm mt-1">Login to continue.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 rounded-md bg-green-50 border border-green-200">
            <p class="text-sm text-green-800">{{ session('success') }}</p>
        </div>
    @endif

    <form wire:submit.prevent="login" class="space-y-6">
        
        <!-- Email Field -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-gray-700">
                Email
            </label>
            <x-ui.input 
                type="email" 
                id="email"
                wire:model="email"
                placeholder="your.email@example.com"
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
        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-gray-700">
                Password
            </label>
            <div x-data="{ showPassword: false }" class="relative">
                <x-ui.input 
                    x-bind:type="showPassword ? 'text' : 'password'"
                    id="password"
                    wire:model="password"
                    placeholder="••••••••"
                    :error="$errors->has('password')"
                    aria-label="Password"
                    aria-required="true"
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                    autocomplete="current-password"
                    class="pr-10"
                />
                <button 
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                    aria-label="Toggle password visibility"
                >
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center space-x-2">
            <input 
                type="checkbox" 
                id="remember" 
                wire:model="remember"
                class="h-4 w-4 text-lgf-blue focus:ring-lgf-blue border-gray-300 rounded"
                aria-label="Remember me"
            >
            <label for="remember" class="text-sm font-medium text-gray-700 leading-none cursor-pointer">
                Remember Me
            </label>
        </div>

        <!-- Submit Button -->
        <div>
            <button
                type="submit"
                class="w-full bg-lgf-blue hover:bg-blue-600 text-white font-medium py-2.5 px-4 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-lgf-blue focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                wire:loading.attr="disabled"
                aria-label="Login"
            >
                <span wire:loading.remove wire:target="login">
                    Login
                </span>
                <span wire:loading wire:target="login" class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Logging in...
                </span>
            </button>
        </div>
    </form>

    <!-- Copyright Footer -->
    <div class="mt-8 pt-6 border-t border-gray-200 text-center text-sm text-gray-600">
        copyright © {{ date('Y') }} lgf. All rights reserved.
    </div>
</div>
