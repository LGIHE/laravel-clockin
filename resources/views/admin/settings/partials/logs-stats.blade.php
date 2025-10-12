<div class="space-y-6">
    <!-- System Stats -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">System Statistics</h3>
            <a 
                href="{{ route('settings.stats') }}" 
                class="text-sm text-blue-600 hover:text-blue-700 font-medium"
            >
                View Detailed Stats
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- PHP Version -->
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">PHP Version</p>
                        <p class="text-lg font-semibold text-gray-900">{{ PHP_VERSION }}</p>
                    </div>
                </div>
            </div>

            <!-- Laravel Version -->
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Laravel Version</p>
                        <p class="text-lg font-semibold text-gray-900">{{ app()->version() }}</p>
                    </div>
                </div>
            </div>

            <!-- Database -->
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Database</p>
                        <p class="text-lg font-semibold text-gray-900 capitalize">{{ config('database.default') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cache Clear Button -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <form action="{{ route('settings.cache.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all cache?');">
                @csrf
                <button 
                    type="submit"
                    class="px-6 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-medium"
                >
                    Clear Application Cache
                </button>
            </form>
        </div>
    </div>

    <!-- System Logs -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">System Logs</h3>
            <div class="flex gap-2">
                <form action="{{ route('settings.logs.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all logs?');">
                    @csrf
                    @method('DELETE')
                    <button 
                        type="submit"
                        class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
                    >
                        Clear Logs
                    </button>
                </form>
            </div>
        </div>
        
        @php
            $logFile = storage_path('logs/laravel.log');
            $logs = '';
            
            if (file_exists($logFile)) {
                $fileContent = file_get_contents($logFile);
                // Get last 50 lines
                $logLines = explode("\n", $fileContent);
                $logs = implode("\n", array_slice($logLines, -50));
            }
        @endphp
        
        @if($logs)
            <div class="bg-gray-900 rounded-lg p-4 overflow-auto" style="max-height: 400px;">
                <pre class="text-xs text-green-400 font-mono">{{ $logs }}</pre>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p>No logs available</p>
            </div>
        @endif
    </div>

    <!-- Application Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Application Information</h3>
        
        <div class="space-y-4">
            <div class="flex justify-between py-3 border-b border-gray-200">
                <span class="text-sm font-medium text-gray-600">Application Name</span>
                <span class="text-sm text-gray-900">{{ \App\Models\SystemSetting::get('app_name', config('app.name')) }}</span>
            </div>
            
            <div class="flex justify-between py-3 border-b border-gray-200">
                <span class="text-sm font-medium text-gray-600">Environment</span>
                <span class="text-sm">
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ config('app.env') === 'production' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst(config('app.env')) }}
                    </span>
                </span>
            </div>
            
            <div class="flex justify-between py-3 border-b border-gray-200">
                <span class="text-sm font-medium text-gray-600">Debug Mode</span>
                <span class="text-sm">
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ config('app.debug') ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                    </span>
                </span>
            </div>
            
            <div class="flex justify-between py-3 border-b border-gray-200">
                <span class="text-sm font-medium text-gray-600">Timezone</span>
                <span class="text-sm text-gray-900">{{ \App\Models\SystemSetting::get('timezone', config('app.timezone')) }}</span>
            </div>
            
            <div class="flex justify-between py-3 border-b border-gray-200">
                <span class="text-sm font-medium text-gray-600">Cache Driver</span>
                <span class="text-sm text-gray-900 capitalize">{{ config('cache.default') }}</span>
            </div>
            
            <div class="flex justify-between py-3">
                <span class="text-sm font-medium text-gray-600">Queue Driver</span>
                <span class="text-sm text-gray-900 capitalize">{{ config('queue.default') }}</span>
            </div>
        </div>
    </div>
</div>
