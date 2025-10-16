<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\SystemSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add global auto clockout time setting if it doesn't exist
        $exists = SystemSetting::where('key', 'global_auto_clockout_time')->exists();
        
        if (!$exists) {
            SystemSetting::create([
                'key' => 'global_auto_clockout_time',
                'value' => '18:00',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Global Auto Clock Out Time - applies to all users without individual settings',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the global auto clockout time setting
        SystemSetting::where('key', 'global_auto_clockout_time')->delete();
    }
};
