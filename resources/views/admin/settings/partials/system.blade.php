<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">System Settings</h3>
    
    <form action="{{ route('settings.update.system') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Global Auto Punch Out Time -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Auto Clock Out Settings
                </h4>
                
                <div class="space-y-4">
                    <!-- Global Auto Clock Out Time -->
                    <div>
                        <label for="global_auto_clockout_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Global Auto Clock Out Time
                        </label>
                        <input 
                            type="time" 
                            name="global_auto_clockout_time" 
                            id="global_auto_clockout_time" 
                            value="{{ old('global_auto_clockout_time', \App\Models\SystemSetting::get('global_auto_clockout_time', '18:00')) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        <p class="mt-1 text-sm text-gray-600">
                            <strong>Global setting:</strong> Automatically clock out all users at this time if they haven't done so manually. 
                            This applies to all users unless they have an individual auto clock-out time set.
                        </p>
                    </div>
                    
                    <div class="bg-white rounded p-3 border border-blue-100">
                        <p class="text-sm text-gray-600">
                            <strong>Note:</strong> Individual user auto clock-out times (set in User Management) will take priority over this global setting. 
                            This global time will only apply to users who don't have a personal auto clock-out time configured.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Timezone -->
            <div>
                <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                    Application Timezone <span class="text-red-500">*</span>
                </label>
                <select 
                    name="timezone" 
                    id="timezone" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    @php
                        $currentTimezone = old('timezone', \App\Models\SystemSetting::get('timezone', config('app.timezone')));
                        $timezones = [
                            'Africa/Kampala' => 'Africa/Kampala (EAT)',
                            'Africa/Nairobi' => 'Africa/Nairobi (EAT)',
                            'UTC' => 'UTC',
                            'America/New_York' => 'America/New York (EST)',
                            'America/Chicago' => 'America/Chicago (CST)',
                            'America/Denver' => 'America/Denver (MST)',
                            'America/Los_Angeles' => 'America/Los Angeles (PST)',
                            'Europe/London' => 'Europe/London (GMT)',
                            'Europe/Paris' => 'Europe/Paris (CET)',
                            'Asia/Dubai' => 'Asia/Dubai (GST)',
                            'Asia/Kolkata' => 'Asia/Kolkata (IST)',
                            'Asia/Shanghai' => 'Asia/Shanghai (CST)',
                            'Asia/Tokyo' => 'Asia/Tokyo (JST)',
                            'Australia/Sydney' => 'Australia/Sydney (AEST)',
                        ];
                    @endphp
                    
                    @foreach($timezones as $value => $label)
                        <option value="{{ $value }}" {{ $currentTimezone == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">The timezone used for all date and time calculations.</p>
            </div>

            <!-- Date Format -->
            <div>
                <label for="date_format" class="block text-sm font-medium text-gray-700 mb-2">
                    Date Format <span class="text-red-500">*</span>
                </label>
                <select 
                    name="date_format" 
                    id="date_format" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    @php
                        $currentDateFormat = old('date_format', \App\Models\SystemSetting::get('date_format', 'Y-m-d'));
                        $dateFormats = [
                            'Y-m-d' => date('Y-m-d') . ' (YYYY-MM-DD)',
                            'd/m/Y' => date('d/m/Y') . ' (DD/MM/YYYY)',
                            'm/d/Y' => date('m/d/Y') . ' (MM/DD/YYYY)',
                            'd-m-Y' => date('d-m-Y') . ' (DD-MM-YYYY)',
                            'F j, Y' => date('F j, Y') . ' (Month Day, Year)',
                        ];
                    @endphp
                    
                    @foreach($dateFormats as $value => $label)
                        <option value="{{ $value }}" {{ $currentDateFormat == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">The format used for displaying dates throughout the application.</p>
            </div>

            <!-- Time Format -->
            <div>
                <label for="time_format" class="block text-sm font-medium text-gray-700 mb-2">
                    Time Format <span class="text-red-500">*</span>
                </label>
                <select 
                    name="time_format" 
                    id="time_format" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    @php
                        $currentTimeFormat = old('time_format', \App\Models\SystemSetting::get('time_format', 'H:i:s'));
                        $timeFormats = [
                            'H:i:s' => date('H:i:s') . ' (24-hour)',
                            'h:i:s A' => date('h:i:s A') . ' (12-hour)',
                            'H:i' => date('H:i') . ' (24-hour without seconds)',
                            'h:i A' => date('h:i A') . ' (12-hour without seconds)',
                        ];
                    @endphp
                    
                    @foreach($timeFormats as $value => $label)
                        <option value="{{ $value }}" {{ $currentTimeFormat == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-sm text-gray-500">The format used for displaying times throughout the application.</p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button 
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                >
                    Save System Settings
                </button>
            </div>
        </div>
    </form>
</div>
