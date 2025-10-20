<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update Maternity Leave to be female-only
        \DB::table('leave_categories')
            ->where('name', 'LIKE', '%Maternity%')
            ->update(['gender_restriction' => 'female']);

        // Update Paternity Leave to be male-only
        \DB::table('leave_categories')
            ->where('name', 'LIKE', '%Paternity%')
            ->update(['gender_restriction' => 'male']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to 'all' for both leave types
        \DB::table('leave_categories')
            ->whereIn('name', ['Maternity Leave', 'Paternity Leave'])
            ->update(['gender_restriction' => 'all']);
    }
};
