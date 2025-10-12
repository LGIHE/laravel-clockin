<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing supervisor_id relationships to the pivot table
        DB::statement('
            INSERT INTO user_supervisor (user_id, supervisor_id, created_at, updated_at)
            SELECT id, supervisor_id, NOW(), NOW()
            FROM users
            WHERE supervisor_id IS NOT NULL
        ');
        
        // Remove the supervisor_id column from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('supervisor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add the supervisor_id column
        Schema::table('users', function (Blueprint $table) {
            $table->char('supervisor_id', 36)->nullable()->after('department_id');
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('set null');
        });
        
        // Migrate back - only take the first supervisor from the pivot table
        DB::statement('
            UPDATE users u
            INNER JOIN (
                SELECT user_id, MIN(supervisor_id) as supervisor_id
                FROM user_supervisor
                GROUP BY user_id
            ) us ON u.id = us.user_id
            SET u.supervisor_id = us.supervisor_id
        ');
    }
};
