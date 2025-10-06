<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Reset your password</h2>
        <p class="mt-2 text-sm text-gray-600">
            Enter your email address and we'll send you a link to reset your password.
        </p>
    </div>

    @if ($emailSent)
        <div class="mb-4 p-4 rounded-md bg-green-50 border border-green-200">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">
                        Email sent successfully!
                    </h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Check your email for a link to reset your password. If it doesn't appear within a few minutes, check your spam folder.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form wire:submit.prevent="sendResetLink"
          x-data="{ 
              email: @entangle('email'),
              emailError: '',
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
              validateForm() {
                  return this.validateEmail();
              }
          }"
          @submit="if (!validateForm()) { $event.preventDefault(); }">
        
        <!-- Email Field -->
        <div class="mb-6">
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

        <!-- Submit Button -->
        <div class="mb-4">
            <x-ui.button 
                type="submit" 
                variant="primary" 
                class="w-full"
                :disabled="$loading"
                aria-label="Send reset link"
            >
                <span wire:loading.remove wire:target="sendResetLink">
                    Send Reset Link
                </span>
                <span wire:loading wire:target="sendResetLink" class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sending...
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
