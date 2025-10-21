<div 
    x-data="{ open: false }"
    @click.away="open = false"
    class="relative"
>
    <!-- Notification Bell Button -->
    <button 
        @click="open = !open"
        type="button"
        class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        aria-label="Notifications"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Unread Count Badge -->
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full min-w-[20px]">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50"
        style="display: none;"
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                @if($unreadCount > 0)
                    <button 
                        wire:click="markAllAsRead"
                        class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors"
                    >
                        Mark all as read
                    </button>
                @endif
            </div>
        </div>

        <!-- Notification List -->
        <div class="max-h-96 overflow-y-auto">
            @if($notifications->isEmpty())
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">No new notifications</p>
                    <p class="text-xs text-gray-500 mt-1">You're all caught up!</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($notifications as $notification)
                        <a 
                            href="{{ $notification->action_url ?? '#' }}"
                            class="block px-4 py-3 hover:bg-gray-50 transition-colors {{ $notification->read ? 'bg-gray-50/50' : 'bg-white' }}"
                            wire:click.prevent="markAsRead('{{ $notification->id }}')"
                            onclick="event.preventDefault(); 
                                     @this.markAsRead('{{ $notification->id }}').then(() => { 
                                         window.location.href = '{{ $notification->action_url ?? '#' }}'; 
                                     });"
                        >
                            <div class="flex items-start space-x-3">
                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 {{ $notification->read ? 'bg-gray-100' : 'bg-blue-100' }} rounded-full flex items-center justify-center">
                                        @if(str_contains($notification->type, 'leave'))
                                            <svg class="w-5 h-5 {{ $notification->read ? 'text-gray-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        @elseif(str_contains($notification->type, 'notice'))
                                            <svg class="w-5 h-5 {{ $notification->read ? 'text-gray-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        @elseif(str_contains($notification->type, 'attendance'))
                                            <svg class="w-5 h-5 {{ $notification->read ? 'text-gray-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @elseif(str_contains($notification->type, 'compensation'))
                                            <svg class="w-5 h-5 {{ $notification->read ? 'text-gray-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @elseif(str_contains($notification->type, 'holiday'))
                                            <svg class="w-5 h-5 {{ $notification->read ? 'text-gray-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 {{ $notification->read ? 'text-gray-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium {{ $notification->read ? 'text-gray-700' : 'text-gray-900' }}">
                                        {{ $notification->title }}
                                    </p>
                                    <p class="text-sm {{ $notification->read ? 'text-gray-500' : 'text-gray-600' }} mt-1 line-clamp-2">
                                        {{ $notification->message }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                    </p>
                                </div>

                                <!-- Unread Indicator -->
                                @if(!$notification->read)
                                    <div class="flex-shrink-0">
                                        <span class="inline-block w-2 h-2 bg-blue-600 rounded-full"></span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Footer -->
        @if($notifications->isNotEmpty())
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                <button 
                    wire:click="openAllNotificationsModal"
                    @click="open = false"
                    class="block w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors"
                >
                    View all notifications
                </button>
            </div>
        @endif
    </div>

    <!-- All Notifications Modal -->
    @if($showAllModal)
        <div 
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" 
            role="dialog" 
            aria-modal="true"
        >
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div 
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                    aria-hidden="true"
                    wire:click="closeAllNotificationsModal"
                ></div>

                <!-- Center modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                    <!-- Header -->
                    <div class="bg-white px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-gray-900" id="modal-title">
                                All Notifications
                            </h3>
                            <div class="flex items-center space-x-3">
                                @if($unreadCount > 0)
                                    <button 
                                        wire:click="markAllAsRead"
                                        class="text-sm text-blue-600 hover:text-blue-800 font-medium transition-colors"
                                    >
                                        Mark all as read
                                    </button>
                                @endif
                                <button 
                                    wire:click="closeAllNotificationsModal"
                                    class="text-gray-400 hover:text-gray-500 focus:outline-none"
                                >
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Notification List -->
                    <div class="bg-white px-6 py-4 max-h-[70vh] overflow-y-auto">
                        @if($allNotifications->isEmpty())
                            <div class="py-12 text-center">
                                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <p class="mt-4 text-base text-gray-600">No notifications yet</p>
                                <p class="text-sm text-gray-500 mt-2">When you receive notifications, they'll appear here</p>
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach($allNotifications as $notification)
                                    <a 
                                        href="{{ $notification->action_url ?? '#' }}"
                                        class="block p-4 rounded-lg border {{ $notification->read ? 'border-gray-200 bg-gray-50/50' : 'border-blue-200 bg-blue-50/30' }} hover:shadow-md transition-all"
                                        wire:click.prevent="markAsRead('{{ $notification->id }}')"
                                        onclick="event.preventDefault(); 
                                                 @this.markAsRead('{{ $notification->id }}').then(() => { 
                                                     window.location.href = '{{ $notification->action_url ?? '#' }}'; 
                                                 });"
                                    >
                                        <div class="flex items-start space-x-4">
                                            <!-- Icon -->
                                            <div class="flex-shrink-0">
                                                <div class="w-12 h-12 {{ $notification->read ? 'bg-gray-100' : 'bg-blue-100' }} rounded-full flex items-center justify-center">
                                                    @if(str_contains($notification->type, 'leave'))
                                                        <svg class="w-6 h-6 {{ $notification->read ? 'text-gray-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                    @elseif(str_contains($notification->type, 'notice'))
                                                        <svg class="w-6 h-6 {{ $notification->read ? 'text-gray-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    @elseif(str_contains($notification->type, 'attendance'))
                                                        <svg class="w-6 h-6 {{ $notification->read ? 'text-gray-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    @elseif(str_contains($notification->type, 'compensation'))
                                                        <svg class="w-6 h-6 {{ $notification->read ? 'text-gray-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    @elseif(str_contains($notification->type, 'holiday'))
                                                        <svg class="w-6 h-6 {{ $notification->read ? 'text-gray-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-6 h-6 {{ $notification->read ? 'text-gray-600' : 'text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Content -->
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between">
                                                    <p class="text-base font-semibold {{ $notification->read ? 'text-gray-700' : 'text-gray-900' }}">
                                                        {{ $notification->title }}
                                                    </p>
                                                    @if(!$notification->read)
                                                        <span class="ml-2 inline-block w-2.5 h-2.5 bg-blue-600 rounded-full flex-shrink-0"></span>
                                                    @endif
                                                </div>
                                                <p class="text-sm {{ $notification->read ? 'text-gray-500' : 'text-gray-600' }} mt-1">
                                                    {{ $notification->message }}
                                                </p>
                                                <div class="flex items-center mt-2 text-xs text-gray-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($notification->created_at)->format('M d, Y \a\t g:i A') }}
                                                    <span class="mx-2">•</span>
                                                    {{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <button 
                            wire:click="closeAllNotificationsModal"
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
