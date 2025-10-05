<x-layouts.app>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100">
        <div class="max-w-4xl mx-auto px-6 py-12">
            <div class="bg-white rounded-lg shadow-xl p-8 md:p-12">
                <div class="text-center">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                        ClockIn Laravel
                    </h1>
                    <p class="text-xl text-gray-600 mb-8">
                        Employee Attendance & Leave Management System
                    </p>
                    
                    <div class="grid md:grid-cols-3 gap-6 mb-8">
                        <div class="p-6 bg-primary-50 rounded-lg">
                            <div class="text-primary-600 text-3xl mb-2">✓</div>
                            <h3 class="font-semibold text-gray-900 mb-2">Laravel 11.x</h3>
                            <p class="text-sm text-gray-600">Latest PHP framework</p>
                        </div>
                        <div class="p-6 bg-primary-50 rounded-lg">
                            <div class="text-primary-600 text-3xl mb-2">⚡</div>
                            <h3 class="font-semibold text-gray-900 mb-2">Livewire 3.x</h3>
                            <p class="text-sm text-gray-600">Dynamic components</p>
                        </div>
                        <div class="p-6 bg-primary-50 rounded-lg">
                            <div class="text-primary-600 text-3xl mb-2">🎨</div>
                            <h3 class="font-semibold text-gray-900 mb-2">Tailwind CSS</h3>
                            <p class="text-sm text-gray-600">Modern styling</p>
                        </div>
                    </div>

                    <div class="space-y-4" x-data="{ message: 'Alpine.js is working!' }">
                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-green-800 font-medium" x-text="message"></p>
                        </div>
                        
                        <div class="flex justify-center gap-4">
                            <button 
                                @click="message = 'Button clicked!'"
                                class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium"
                            >
                                Test Alpine.js
                            </button>
                            <a 
                                href="/docs" 
                                class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-colors font-medium"
                            >
                                View Documentation
                            </a>
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Setup Complete ✓</h3>
                        <ul class="text-left max-w-md mx-auto space-y-2 text-gray-600">
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                Laravel 11.x installed
                            </li>
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                Livewire 3.x configured
                            </li>
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                Alpine.js integrated
                            </li>
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                Tailwind CSS ready
                            </li>
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                Laravel Sanctum configured
                            </li>
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                Testing environment ready
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
