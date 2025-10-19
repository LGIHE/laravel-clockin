<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('category'); // e.g., 'users', 'leaves', 'attendance', 'reports'
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('user_level_permission', function (Blueprint $table) {
            $table->id();
            $table->string('user_level_id');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->foreign('user_level_id')->references('id')->on('user_levels')->onDelete('cascade');
            $table->unique(['user_level_id', 'permission_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_level_permission');
        Schema::dropIfExists('permissions');
    }
};
