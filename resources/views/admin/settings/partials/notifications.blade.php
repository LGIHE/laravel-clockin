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
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
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

            <!-- Enable Leave Notifications -->
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
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

            <!-- Enable Attendance Notifications -->
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
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

            <!-- Enable Task Notifications -->
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
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
