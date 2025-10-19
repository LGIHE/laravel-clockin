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
        // First, drop existing constraints if they exist
        Schema::table('user_supervisor', function (Blueprint $table) {
            // Drop existing foreign keys
            $this->dropForeignKeyIfExists('user_supervisor', 'user_supervisor_user_id_foreign');
            $this->dropForeignKeyIfExists('user_supervisor', 'user_supervisor_supervisor_id_foreign');
            
            // Drop existing unique constraints
            $this->dropIndexIfExists('user_supervisor', 'user_supervisor_user_id_supervisor_id_unique');
            $this->dropIndexIfExists('user_supervisor', 'user_supervisor_type_unique');
        });

        // Now add the new constraints
        Schema::table('user_supervisor', function (Blueprint $table) {
            $table->unique(['user_id', 'supervisor_type'], 'user_supervisor_type_unique');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('supervisor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_supervisor', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['supervisor_id']);
            $table->dropUnique('user_supervisor_type_unique');
            
            // Restore the original unique constraint
            $table->unique(['user_id', 'supervisor_id']);
        });
    }

    /**
     * Drop a foreign key if it exists
     */
    private function dropForeignKeyIfExists(string $table, string $foreignKey): void
    {
        $connection = Schema::getConnection();
        $doctrineSchemaManager = $connection->getDoctrineSchemaManager();
        $foreignKeys = $doctrineSchemaManager->listTableForeignKeys($table);
        
        foreach ($foreignKeys as $fk) {
            if ($fk->getName() === $foreignKey) {
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$foreignKey}`");
                break;
            }
        }
    }

    /**
     * Drop an index if it exists
     */
    private function dropIndexIfExists(string $table, string $index): void
    {
        $connection = Schema::getConnection();
        $doctrineSchemaManager = $connection->getDoctrineSchemaManager();
        $indexes = $doctrineSchemaManager->listTableIndexes($table);
        
        if (isset($indexes[$index])) {
            DB::statement("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
        }
    }
};
