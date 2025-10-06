@props([
    'collapsed' => false
])

<aside 
    x-data="{ collapsed: @js($collapsed) }"
    :class="collapsed ? 'w-16' : 'w-64'"
    {{ $attributes->merge(['class' => 'bg-white border-r border-gray-200 transition-all duration-300 ease-in-out']) }}
>
    <div class="h-full flex flex-col">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <div x-show="!collapsed" class="flex items-center space-x-2">
                <span class="text-xl font-bold text-gray-900">ClockIn</span>
            </div>
            <button 
                @click="collapsed = !collapsed"
                class="p-2 rounded-md hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Sidebar Content -->
        <nav class="flex-1 overflow-y-auto p-4">
            {{ $slot }}
        </nav>
    </div>
</aside>
