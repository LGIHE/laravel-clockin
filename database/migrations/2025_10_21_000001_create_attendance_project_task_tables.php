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
        // Create pivot table for attendance-project relationship
        Schema::create('attendance_project', function (Blueprint $table) {
            $table->char('attendance_id', 36);
            $table->char('project_id', 36);
            $table->timestamps();
            
            $table->primary(['attendance_id', 'project_id']);
            $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });

        // Create pivot table for attendance-task relationship
        Schema::create('attendance_task', function (Blueprint $table) {
            $table->char('attendance_id', 36);
            $table->char('task_id', 36);
            $table->string('status')->default('in-progress'); // Task status for this attendance session
            $table->timestamps();
            
            $table->primary(['attendance_id', 'task_id']);
            $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
        });

        // Migrate existing data from attendances table to pivot tables
        DB::statement('
            INSERT INTO attendance_project (attendance_id, project_id, created_at, updated_at)
            SELECT id, project_id, created_at, updated_at
            FROM attendances
            WHERE project_id IS NOT NULL
        ');

        DB::statement('
            INSERT INTO attendance_task (attendance_id, task_id, status, created_at, updated_at)
            SELECT id, task_id, COALESCE(task_status, "in-progress"), created_at, updated_at
            FROM attendances
            WHERE task_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_task');
        Schema::dropIfExists('attendance_project');
    }
};
