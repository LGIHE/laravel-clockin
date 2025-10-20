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
        Schema::create('compensation_leave_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->date('work_date');
            $table->enum('work_type', ['weekend', 'holiday'])->default('weekend');
            $table->decimal('days_requested', 3, 1)->default(1.0); // 1.0 or 1.5 days
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'supervisor_approved', 'hr_effected', 'rejected'])->default('pending');
            $table->uuid('supervisor_approved_by')->nullable();
            $table->timestamp('supervisor_approved_at')->nullable();
            $table->uuid('hr_effected_by')->nullable();
            $table->timestamp('hr_effected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('supervisor_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('hr_effected_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compensation_leave_requests');
    }
};
