<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Email Configuration</h3>
    
    <form action="{{ route('settings.update.email') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Mail Mailer -->
            <div>
                <label for="mail_mailer" class="block text-sm font-medium text-gray-700 mb-2">
                    Mail Mailer <span class="text-red-500">*</span>
                </label>
                <select 
                    name="mail_mailer" 
                    id="mail_mailer" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                    <option value="smtp" {{ old('mail_mailer', \App\Models\SystemSetting::get('mail_mailer', 'smtp')) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                    <option value="sendmail" {{ old('mail_mailer', \App\Models\SystemSetting::get('mail_mailer')) == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                    <option value="mailgun" {{ old('mail_mailer', \App\Models\SystemSetting::get('mail_mailer')) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                    <option value="ses" {{ old('mail_mailer', \App\Models\SystemSetting::get('mail_mailer')) == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                </select>
            </div>

            <!-- Mail Host -->
            <div>
                <label for="mail_host" class="block text-sm font-medium text-gray-700 mb-2">
                    Mail Host <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="mail_host" 
                    id="mail_host" 
                    value="{{ old('mail_host', \App\Models\SystemSetting::get('mail_host', 'smtp.gmail.com')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                <p class="mt-1 text-sm text-gray-500">SMTP server hostname (e.g., smtp.gmail.com).</p>
            </div>

            <!-- Mail Port -->
            <div>
                <label for="mail_port" class="block text-sm font-medium text-gray-700 mb-2">
                    Mail Port <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    name="mail_port" 
                    id="mail_port" 
                    value="{{ old('mail_port', \App\Models\SystemSetting::get('mail_port', 587)) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                <p class="mt-1 text-sm text-gray-500">SMTP port (usually 587 for TLS or 465 for SSL).</p>
            </div>

            <!-- Mail Username -->
            <div>
                <label for="mail_username" class="block text-sm font-medium text-gray-700 mb-2">
                    Mail Username
                </label>
                <input 
                    type="text" 
                    name="mail_username" 
                    id="mail_username" 
                    value="{{ old('mail_username', \App\Models\SystemSetting::get('mail_username')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                <p class="mt-1 text-sm text-gray-500">SMTP username (usually your email address).</p>
            </div>

            <!-- Mail Password -->
            <div>
                <label for="mail_password" class="block text-sm font-medium text-gray-700 mb-2">
                    Mail Password
                </label>
                <input 
                    type="password" 
                    name="mail_password" 
                    id="mail_password" 
                    value="{{ old('mail_password', \App\Models\SystemSetting::get('mail_password')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter password to change"
                >
                <p class="mt-1 text-sm text-gray-500">SMTP password or app-specific password.</p>
            </div>

            <!-- Mail Encryption -->
            <div>
                <label for="mail_encryption" class="block text-sm font-medium text-gray-700 mb-2">
                    Mail Encryption
                </label>
                <select 
                    name="mail_encryption" 
                    id="mail_encryption" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="tls" {{ old('mail_encryption', \App\Models\SystemSetting::get('mail_encryption', 'tls')) == 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ old('mail_encryption', \App\Models\SystemSetting::get('mail_encryption')) == 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="" {{ old('mail_encryption', \App\Models\SystemSetting::get('mail_encryption')) == '' ? 'selected' : '' }}>None</option>
                </select>
            </div>

            <!-- Mail From Address -->
            <div>
                <label for="mail_from_address" class="block text-sm font-medium text-gray-700 mb-2">
                    From Email Address <span class="text-red-500">*</span>
                </label>
                <input 
                    type="email" 
                    name="mail_from_address" 
                    id="mail_from_address" 
                    value="{{ old('mail_from_address', \App\Models\SystemSetting::get('mail_from_address', 'noreply@example.com')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                <p class="mt-1 text-sm text-gray-500">Email address that will appear in the "From" field.</p>
            </div>

            <!-- Mail From Name -->
            <div>
                <label for="mail_from_name" class="block text-sm font-medium text-gray-700 mb-2">
                    From Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="mail_from_name" 
                    id="mail_from_name" 
                    value="{{ old('mail_from_name', \App\Models\SystemSetting::get('mail_from_name', config('app.name'))) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required
                >
                <p class="mt-1 text-sm text-gray-500">Name that will appear in the "From" field.</p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button 
                    type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                >
                    Save Email Settings
                </button>
            </div>
        </div>
    </form>
</div>
