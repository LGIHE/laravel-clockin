<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Notification Settings</h3>
    
    <form action="{{ route('settings.update.notification') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <p class="text-sm text-gray-600 mb-4">
                Control which notifications are enabled throughout the system.
            </p>

            <!-- Enable Email Notifications -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label for="enable_email_notifications" class="block text-sm font-medium text-gray-700">
                            Email Notifications
                        </label>
                        <p class="text-sm text-gray-500 mt-1">
                            Send email notifications to users.
                        </p>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="enable_email_notifications" 
                                id="enable_email_notifications" 
                                value="1"
                                {{ old('enable_email_notifications', \App\Models\SystemSetting::get('enable_email_notifications', true)) ? 'checked' : '' }}
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Enable Leave Notifications -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label for="enable_leave_notifications" class="block text-sm font-medium text-gray-700">
                            Leave Notifications
                        </label>
                        <p class="text-sm text-gray-500 mt-1">
                            Notify supervisors and admins when leave requests are submitted or approved.
                        </p>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="enable_leave_notifications" 
                                id="enable_leave_notifications" 
                                value="1"
                                {{ old('enable_leave_notifications', \App\Models\SystemSetting::get('enable_leave_notifications', true)) ? 'checked' : '' }}
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Enable Attendance Notifications -->
            <div class="p-4 bg-blue-50 rounded-lg border-2 border-blue-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <label for="enable_attendance_notifications" class="block text-sm font-medium text-gray-700">
                            Attendance Notifications
                        </label>
                        <p class="text-sm text-gray-500 mt-1">
                            Notify users about attendance-related events like late arrivals or missed punch-outs.
                        </p>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="enable_attendance_notifications" 
                                id="enable_attendance_notifications" 
                                value="1"
                                {{ old('enable_attendance_notifications', \App\Models\SystemSetting::get('enable_attendance_notifications', true)) ? 'checked' : '' }}
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Clockin Reminder Time -->
                <div class="mt-4 pt-4 border-t border-blue-200">
                    <label for="clockin_reminder_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Daily Clockin Reminder Time
                    </label>
                    <p class="text-sm text-gray-500 mb-3">
                        Set the time when daily clockin reminder emails should be sent to selected staff members.
                    </p>
                    <input 
                        type="time" 
                        name="clockin_reminder_time" 
                        id="clockin_reminder_time"
                        value="{{ old('clockin_reminder_time', \App\Models\SystemSetting::get('clockin_reminder_time', '08:00')) }}"
                        class="w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <!-- Clockin Notification Recipients -->
                <div class="mt-4 pt-4 border-t border-blue-200">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Clockin Reminder Recipients
                        </label>
                        <span id="recipient-count" class="text-sm font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                            {{ count($selectedRecipients ?? []) }} selected
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mb-3">
                        Select staff members who should receive daily clockin reminder emails.
                    </p>
                    
                    @php
                        $selectedRecipients = old('clockin_notification_recipients', \App\Models\SystemSetting::get('clockin_notification_recipients', []));
                        // Ensure it's an array
                        if (!is_array($selectedRecipients)) {
                            $selectedRecipients = [];
                        }
                        $allUsers = \App\Models\User::where('status', 1)->orderBy('name')->get();
                        
                        // Separate selected and unselected users for better visibility
                        $selectedUsers = $allUsers->filter(function($user) use ($selectedRecipients) {
                            return in_array($user->id, $selectedRecipients);
                        });
                        $unselectedUsers = $allUsers->filter(function($user) use ($selectedRecipients) {
                            return !in_array($user->id, $selectedRecipients);
                        });
                    @endphp

                    <!-- Quick Actions -->
                    <div class="flex gap-2 mb-3">
                        <button 
                            type="button" 
                            onclick="selectAllRecipients()" 
                            class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors"
                        >
                            Select All
                        </button>
                        <button 
                            type="button" 
                            onclick="deselectAllRecipients()" 
                            class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors"
                        >
                            Deselect All
                        </button>
                    </div>

                    <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-lg p-3 bg-white">
                        <div class="space-y-2">
                            @if($selectedUsers->isNotEmpty())
                                <!-- Selected Users Section -->
                                <div class="mb-3">
                                    <p class="text-xs font-semibold text-blue-600 uppercase mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Selected ({{ $selectedUsers->count() }})
                                    </p>
                                    @foreach($selectedUsers as $user)
                                        <label class="flex items-center p-2 bg-blue-50 border border-blue-200 rounded cursor-pointer hover:bg-blue-100 transition-colors">
                                            <input 
                                                type="checkbox" 
                                                name="clockin_notification_recipients[]" 
                                                value="{{ $user->id }}"
                                                checked
                                                class="recipient-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                onchange="updateRecipientCount()"
                                            >
                                            <span class="ml-3 text-sm text-gray-900 font-medium">
                                                {{ $user->name }}
                                                <span class="text-gray-600 font-normal">({{ $user->email }})</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            
                            @if($unselectedUsers->isNotEmpty())
                                <!-- Unselected Users Section -->
                                <div>
                                    @if($selectedUsers->isNotEmpty())
                                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2 mt-3 pt-3 border-t border-gray-200">
                                            Available Staff ({{ $unselectedUsers->count() }})
                                        </p>
                                    @endif
                                    @foreach($unselectedUsers as $user)
                                        <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition-colors">
                                            <input 
                                                type="checkbox" 
                                                name="clockin_notification_recipients[]" 
                                                value="{{ $user->id }}"
                                                class="recipient-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                                onchange="updateRecipientCount()"
                                            >
                                            <span class="ml-3 text-sm text-gray-700">
                                                {{ $user->name }}
                                                <span class="text-gray-500">({{ $user->email }})</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            
                            @if($allUsers->isEmpty())
                                <p class="text-sm text-gray-500 text-center py-4">No active users found</p>
                            @endif
                        </div>
                    </div>
                    
                    <p class="text-xs text-gray-500 mt-2">
                        💡 Tip: Selected users will receive daily reminders at the time specified above. Deselect any user to stop their reminders.
                    </p>
                </div>

                <script>
                    function updateRecipientCount() {
                        const checkboxes = document.querySelectorAll('.recipient-checkbox');
                        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                        document.getElementById('recipient-count').textContent = checkedCount + ' selected';
                    }
                    
                    function selectAllRecipients() {
                        document.querySelectorAll('.recipient-checkbox').forEach(cb => cb.checked = true);
                        updateRecipientCount();
                    }
                    
                    function deselectAllRecipients() {
                        document.querySelectorAll('.recipient-checkbox').forEach(cb => cb.checked = false);
                        updateRecipientCount();
                    }
                </script>
            </div>

            <!-- Enable Notice Notifications -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label for="enable_notice_notifications" class="block text-sm font-medium text-gray-700">
                            Notice Notifications
                        </label>
                        <p class="text-sm text-gray-500 mt-1">
                            Notify users when new notices or announcements are posted.
                        </p>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="enable_notice_notifications" 
                                id="enable_notice_notifications" 
                                value="1"
                                {{ old('enable_notice_notifications', \App\Models\SystemSetting::get('enable_notice_notifications', true)) ? 'checked' : '' }}
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Enable Task Notifications -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <label for="enable_task_notifications" class="block text-sm font-medium text-gray-700">
                            Task Notifications
                        </label>
                        <p class="text-sm text-gray-500 mt-1">
                            Notify users when tasks are assigned or their status changes.
                        </p>
                    </div>
                    <div class="ml-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="enable_task_notifications" 
                                id="enable_task_notifications" 
                                value="1"
                                {{ old('enable_task_notifications', \App\Models\SystemSetting::get('enable_task_notifications', true)) ? 'checked' : '' }}
                                class="sr-only peer"
                            >
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button 
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                >
                    Save Notification Settings
                </button>
            </div>
        </div>
    </form>
</div>
