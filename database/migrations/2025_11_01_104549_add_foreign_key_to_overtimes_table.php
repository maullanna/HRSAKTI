<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddForeignKeyToOvertimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if approved_by column exists
        if (Schema::hasColumn('overtimes', 'approved_by')) {
            // First, clean up any invalid foreign key references
            // Set NULL for any approved_by that doesn't exist in users table
            DB::statement('UPDATE overtimes 
                SET approved_by = NULL 
                WHERE approved_by IS NOT NULL 
                AND approved_by NOT IN (SELECT id FROM users)');

            // Add foreign key constraint using raw SQL to handle existing data
            try {
                DB::statement('ALTER TABLE `overtimes` 
                    ADD CONSTRAINT `overtimes_approved_by_foreign` 
                    FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL');
            } catch (\Exception $e) {
                // If constraint already exists, skip
                if (strpos($e->getMessage(), 'Duplicate key name') === false) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('overtimes', 'approved_by')) {
            try {
                DB::statement('ALTER TABLE `overtimes` DROP FOREIGN KEY `overtimes_approved_by_foreign`');
            } catch (\Exception $e) {
                // Ignore if constraint doesn't exist
            }
        }
    }
}
