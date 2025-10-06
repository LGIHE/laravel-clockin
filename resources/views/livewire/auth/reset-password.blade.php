<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Create new password</h2>
        <p class="mt-2 text-sm text-gray-600">
            Please enter your new password below.
        </p>
    </div>

    <form wire:submit.prevent="resetPassword"
          x-data="{ 
              email: @entangle('email'),
              password: @entangle('password'),
              passwordConfirmation: @entangle('password_confirmation'),
              emailError: '',
              passwordError: '',
              confirmError: '',
              showPassword: false,
              showConfirmPassword: false,
              validateEmail() {
                  if (!this.email) {
                      this.emailError = 'Email is required';
                      return false;
                  }
                  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                  if (!emailRegex.test(this.email)) {
                      this.emailError = 'Please enter a valid email address';
                      return false;
                  }
                  this.emailError = '';
                  return true;
              },
              validatePassword() {
                  if (!this.password) {
                      this.passwordError = 'Password is required';
                      return false;
                  }
                  if (this.password.length < 6) {
                      this.passwordError = 'Password must be at least 6 characters';
                      return false;
                  }
                  this.passwordError = '';
                  return true;
              },
              validateConfirmation() {
                  if (!this.passwordConfirmation) {
                      this.confirmError = 'Please confirm your password';
                      return false;
                  }
                  if (this.password !== this.passwordConfirmation) {
                      this.confirmError = 'Passwords do not match';
                      return false;
                  }
                  this.confirmError = '';
                  return true;
              },
              validateForm() {
                  const emailValid = this.validateEmail();
                  const passwordValid = this.validatePassword();
                  const confirmValid = this.validateConfirmation();
                  return emailValid && passwordValid && confirmValid;
              }
          }">
        
        <!-- Email Field -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                Email Address
            </label>
            <x-ui.input 
                type="email" 
                id="email"
                wire:model="email"
                x-model="email"
                @blur="validateEmail()"
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
            <p x-show="emailError && !{{ $errors->has('email') ? 'true' : 'false' }}" 
               x-text="emailError" 
               class="mt-1 text-sm text-red-600"
               role="alert"
               style="display: none;"></p>
        </div>

        <!-- Password Field -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                New Password
            </label>
            <div class="relative">
                <x-ui.input 
                    x-bind:type="showPassword ? 'text' : 'password'"
                    id="password"
                    wire:model="password"
                    x-model="password"
                    @blur="validatePassword()"
                    placeholder="Enter new password"
                    :error="$errors->has('password')"
                    aria-label="New Password"
                    aria-required="true"
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                    autocomplete="new-password"
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
            <p x-show="passwordError && !{{ $errors->has('password') ? 'true' : 'false' }}" 
               x-text="passwordError" 
               class="mt-1 text-sm text-red-600"
               role="alert"
               style="display: none;"></p>
        </div>

        <!-- Confirm Password Field -->
        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                Confirm New Password
            </label>
            <div class="relative">
                <x-ui.input 
                    x-bind:type="showConfirmPassword ? 'text' : 'password'"
                    id="password_confirmation"
                    wire:model="password_confirmation"
                    x-model="passwordConfirmation"
                    @blur="validateConfirmation()"
                    placeholder="Confirm new password"
                    :error="$errors->has('password_confirmation')"
                    aria-label="Confirm New Password"
                    aria-required="true"
                    aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}"
                    autocomplete="new-password"
                    class="pr-10"
                />
                <button 
                    type="button"
                    @click="showConfirmPassword = !showConfirmPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                    aria-label="Toggle password visibility"
                >
                    <svg x-show="!showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
            <p x-show="confirmError && !{{ $errors->has('password_confirmation') ? 'true' : 'false' }}" 
               x-text="confirmError" 
               class="mt-1 text-sm text-red-600"
               role="alert"
               style="display: none;"></p>
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
