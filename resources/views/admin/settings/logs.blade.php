<x-layouts.app title="System Logs">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">System Logs</h1>
                <p class="mt-1 text-sm text-gray-600">View recent application logs</p>
            </div>
            <a 
                href="{{ route('settings.index') }}" 
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium"
            >
                Back to Settings
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Logs Display -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Application Logs</h3>
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
            
            @if($logs)
                <div class="bg-gray-900 rounded-lg p-4 overflow-auto" style="max-height: 600px;">
                    <pre class="text-xs text-green-400 font-mono whitespace-pre-wrap">{{ $logs }}</pre>
                </div>
            @else
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p>No logs available</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
