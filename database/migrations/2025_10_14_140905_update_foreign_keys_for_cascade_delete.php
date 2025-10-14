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
        // Drop existing foreign keys and recreate with cascade delete
        
        // Attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Leaves table
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('leaves', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Drop deleted_at column from users table (remove soft deletes)
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        // Also drop soft deletes from attendances and leaves if not needed
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore soft deletes
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Restore original foreign keys without cascade
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::table('leaves', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};
