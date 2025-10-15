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
        // Create user_levels table
        Schema::create('user_levels', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Create departments table
        Schema::create('departments', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create designations table
        Schema::create('designations', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('name')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create users table
        Schema::create('users', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('user_level_id', 36);
            $table->char('designation_id', 36)->nullable();
            $table->char('department_id', 36)->nullable();
            $table->longText('project_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->tinyInteger('status')->default(1);
            $table->string('ip')->nullable();
            $table->time('last_in_time')->nullable();
            $table->time('auto_punch_out_time')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_level_id')->references('id')->on('user_levels');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('designation_id')->references('id')->on('designations');
            $table->index('email');
        });

        // Create attendances table
        Schema::create('attendances', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('user_id', 36);
            $table->dateTime('in_time');
            $table->string('in_message')->nullable();
            $table->dateTime('out_time')->nullable();
            $table->string('out_message')->nullable();
            $table->unsignedInteger('worked')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->index('in_time');
        });

        // Create leave_categories table
        Schema::create('leave_categories', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('name')->unique();
            $table->tinyInteger('max_in_year');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create leave_statuses table
        Schema::create('leave_statuses', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // Create leaves table
        Schema::create('leaves', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('user_id', 36);
            $table->char('leave_category_id', 36);
            $table->char('leave_status_id', 36);
            $table->date('date');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('leave_category_id')->references('id')->on('leave_categories');
            $table->foreign('leave_status_id')->references('id')->on('leave_statuses');
            $table->index('leave_status_id');
        });

        // Create projects table
        Schema::create('projects', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 20)->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();
        });

        // Create holidays table
        Schema::create('holidays', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->date('date')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create notices table
        Schema::create('notices', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('subject');
            $table->text('message');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('leave_statuses');
        Schema::dropIfExists('leave_categories');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('users');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('user_levels');
    }
};
