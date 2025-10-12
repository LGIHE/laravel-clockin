<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">General Settings</h3>
    
    <form action="{{ route('settings.update.general') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Application Name -->
            <div>
                <label for="app_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Application Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="app_name" 
                    id="app_name" 
                    value="{{ old('app_name', \App\Models\SystemSetting::get('app_name', config('app.name'))) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                <p class="mt-1 text-sm text-gray-500">The name of your application displayed throughout the system.</p>
            </div>

            <!-- Application Address -->
            <div>
                <label for="app_address" class="block text-sm font-medium text-gray-700 mb-2">
                    Application Address
                </label>
                <textarea 
                    name="app_address" 
                    id="app_address" 
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >{{ old('app_address', \App\Models\SystemSetting::get('app_address')) }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Physical address of your organization.</p>
            </div>

            <!-- Application Contact -->
            <div>
                <label for="app_contact" class="block text-sm font-medium text-gray-700 mb-2">
                    Contact Number
                </label>
                <input 
                    type="text" 
                    name="app_contact" 
                    id="app_contact" 
                    value="{{ old('app_contact', \App\Models\SystemSetting::get('app_contact')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                <p class="mt-1 text-sm text-gray-500">Contact phone number for your organization.</p>
            </div>

            <!-- Application Email -->
            <div>
                <label for="app_email" class="block text-sm font-medium text-gray-700 mb-2">
                    Contact Email
                </label>
                <input 
                    type="email" 
                    name="app_email" 
                    id="app_email" 
                    value="{{ old('app_email', \App\Models\SystemSetting::get('app_email')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                <p class="mt-1 text-sm text-gray-500">Contact email address for your organization.</p>
            </div>

            <!-- Application Logo -->
            <div>
                <label for="app_logo" class="block text-sm font-medium text-gray-700 mb-2">
                    Application Logo
                </label>
                
                @php
                    $currentLogo = \App\Models\SystemSetting::get('app_logo');
                @endphp
                
                @if($currentLogo)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $currentLogo) }}" alt="Current Logo" class="h-20 w-auto border border-gray-300 rounded-lg p-2">
                        <p class="mt-1 text-sm text-gray-500">Current logo</p>
                    </div>
                @endif
                
                <input 
                    type="file" 
                    name="app_logo" 
                    id="app_logo" 
                    accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                <p class="mt-1 text-sm text-gray-500">Upload a logo for your application (JPEG, PNG, JPG, GIF, max 2MB).</p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button 
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                >
                    Save General Settings
                </button>
            </div>
        </div>
    </form>
</div>
