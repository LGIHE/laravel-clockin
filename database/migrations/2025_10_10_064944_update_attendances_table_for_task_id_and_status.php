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
        Schema::table('attendances', function (Blueprint $table) {
            // Drop the old task column and add task_id
            $table->dropColumn('task');
            $table->char('task_id', 36)->nullable()->after('project_id');
            $table->enum('task_status', ['in-progress', 'on-hold', 'completed'])->nullable()->after('task_id');
            
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['task_id']);
            $table->dropColumn(['task_id', 'task_status']);
            $table->string('task')->nullable()->after('project_id');
        });
    }
};
