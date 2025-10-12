<x-layouts.app title="System Statistics">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">System Statistics</h1>
                <p class="mt-1 text-sm text-gray-600">Detailed system information and statistics</p>
            </div>
            <a 
                href="{{ route('settings.index') }}" 
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium"
            >
                Back to Settings
            </a>
        </div>

        <!-- Server Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Server Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex justify-between py-3 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-600">PHP Version</span>
                        <span class="text-sm text-gray-900">{{ $stats['php_version'] }}</span>
                    </div>
                    
                    <div class="flex justify-between py-3 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-600">Laravel Version</span>
                        <span class="text-sm text-gray-900">{{ $stats['laravel_version'] }}</span>
                    </div>
                    
                    <div class="flex justify-between py-3 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-600">Server Software</span>
                        <span class="text-sm text-gray-900">{{ $stats['server_software'] }}</span>
                    </div>
                    
                    <div class="flex justify-between py-3 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-600">Database</span>
                        <span class="text-sm text-gray-900 capitalize">{{ $stats['database'] }}</span>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between py-3 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-600">Cache Driver</span>
                        <span class="text-sm text-gray-900 capitalize">{{ $stats['cache_driver'] }}</span>
                    </div>
                    
                    <div class="flex justify-between py-3 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-600">Queue Driver</span>
                        <span class="text-sm text-gray-900 capitalize">{{ $stats['queue_driver'] }}</span>
                    </div>
                    
                    <div class="flex justify-between py-3 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-600">Memory Limit</span>
                        <span class="text-sm text-gray-900">{{ $stats['memory_limit'] }}</span>
                    </div>
                    
                    <div class="flex justify-between py-3 border-b border-gray-200">
                        <span class="text-sm font-medium text-gray-600">Max Execution Time</span>
                        <span class="text-sm text-gray-900">{{ $stats['max_execution_time'] }} seconds</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Disk Space Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Disk Space</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-6 bg-blue-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Free Disk Space</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['disk_space'] }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 bg-green-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Total Disk Space</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_disk_space'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Environment Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Environment</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between py-3 border-b border-gray-200">
                    <span class="text-sm font-medium text-gray-600">Application Environment</span>
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
                    <span class="text-sm font-medium text-gray-600">Application URL</span>
                    <span class="text-sm text-gray-900">{{ config('app.url') }}</span>
                </div>
                
                <div class="flex justify-between py-3">
                    <span class="text-sm font-medium text-gray-600">Timezone</span>
                    <span class="text-sm text-gray-900">{{ \App\Models\SystemSetting::get('timezone', config('app.timezone')) }}</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">System Actions</h3>
            
            <div class="flex gap-4">
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
    </div>
</x-layouts.app>
