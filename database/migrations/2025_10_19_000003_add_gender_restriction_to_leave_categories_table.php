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
        Schema::table('leave_categories', function (Blueprint $table) {
            $table->enum('gender_restriction', ['male', 'female', 'all'])->default('all')->after('max_in_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_categories', function (Blueprint $table) {
            $table->dropColumn('gender_restriction');
        });
    }
};
