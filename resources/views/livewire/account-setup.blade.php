<div class="min-h-screen bg-gradient-to-br from-purple-600 to-indigo-700 flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-2xl p-8">
        @if (!$tokenValid)
            <div class="text-center">
                <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Invalid or Used Link</h2>
                <p class="mt-2 text-gray-600">
                    @if ($tokenExpired)
                        This setup link has expired. Please contact your administrator to request a new account setup link.
                    @else
                        This setup link is invalid or has already been used. If you need assistance, please contact your administrator.
                    @endif
                </p>
                <a href="{{ route('login') }}" class="mt-6 inline-block px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                    Go to Login
                </a>
            </div>
        @elseif ($setupComplete)
            <!-- Welcome Dialog -->
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                    <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="mt-4 text-3xl font-bold text-gray-900">🎉 Welcome to ClockIn!</h2>
                <p class="mt-3 text-lg text-gray-600">
                    Your account has been successfully set up, {{ $user->name }}!
                </p>
                <p class="mt-2 text-gray-500">
                    You're now logged in and ready to start using the system.
                </p>
                <button 
                    wire:click="redirectToDashboard"
                    class="mt-6 px-8 py-3 bg-lgf-blue hover:bg-blue-600 text-white rounded-md font-semibold shadow-lg transform transition hover:scale-105"
                >
                    Go to Dashboard
                </button>
            </div>
        @else
            <!-- Password Setup Form -->
            <div>
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Complete Your Account Setup</h2>
                    <p class="mt-2 text-gray-600">Welcome, {{ $user->name }}! Create your password to get started.</p>
                </div>

                <form wire:submit.prevent="setupPassword">
                    <div class="space-y-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Your Email
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                value="{{ $user->email }}" 
                                disabled
                                class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500"
                            >
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Create Password
                            </label>
                            <input 
                                type="password" 
                                id="password" 
                                wire:model="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                placeholder="Enter your password"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password
                            </label>
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                wire:model="password_confirmation"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                placeholder="Confirm your password"
                            >
                        </div>

                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Password must be at least 8 characters and contain uppercase, lowercase, and numbers.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <button 
                            type="submit"
                            class="w-full px-4 py-3 bg-lgf-blue hover:bg-blue-600 text-white rounded-md font-semibold shadow-lg transform transition hover:scale-105"
                        >
                            Complete Setup & Login
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
