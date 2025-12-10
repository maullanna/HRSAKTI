<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUniqueConstraintsToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check and add unique constraint for employee_code
        try {
            $indexes = DB::select("SHOW INDEXES FROM `employees` WHERE Key_name = 'employees_employee_code_unique'");
            if (empty($indexes)) {
                DB::statement('ALTER TABLE `employees` ADD UNIQUE KEY `employees_employee_code_unique` (`employee_code`)');
            }
        } catch (\Exception $e) {
            // Ignore if already exists or error
        }

        // Check and add unique constraint for email (if not already unique)
        try {
            $indexes = DB::select("SHOW INDEXES FROM `employees` WHERE Key_name = 'employees_email_unique'");
            if (empty($indexes)) {
                // First, handle NULL values - set empty strings to NULL
                DB::statement("UPDATE `employees` SET `email` = NULL WHERE `email` = '' OR `email` IS NULL");
                
                // Add unique constraint for email
                DB::statement('ALTER TABLE `employees` ADD UNIQUE KEY `employees_email_unique` (`email`)');
            }
        } catch (\Exception $e) {
            // Ignore if already exists or error
        }

        // Check and add unique constraint for nik
        try {
            $indexes = DB::select("SHOW INDEXES FROM `employees` WHERE Key_name = 'employees_nik_unique'");
            if (empty($indexes)) {
                // First, handle NULL values - set empty strings to NULL
                DB::statement("UPDATE `employees` SET `nik` = NULL WHERE `nik` = '' OR `nik` IS NULL");
                
                // Remove duplicate NIKs (keep the first one, set others to NULL)
                $duplicates = DB::select("
                    SELECT `nik`, COUNT(*) as count 
                    FROM `employees` 
                    WHERE `nik` IS NOT NULL AND `nik` != ''
                    GROUP BY `nik` 
                    HAVING count > 1
                ");
                
                foreach ($duplicates as $dup) {
                    $employees = DB::select("
                        SELECT `id_employees` 
                        FROM `employees` 
                        WHERE `nik` = ? 
                        ORDER BY `id_employees` ASC
                    ", [$dup->nik]);
                    
                    // Keep first, set others to NULL
                    for ($i = 1; $i < count($employees); $i++) {
                        DB::statement("UPDATE `employees` SET `nik` = NULL WHERE `id_employees` = ?", [$employees[$i]->id_employees]);
                    }
                }
                
                // Add unique constraint for nik
                DB::statement('ALTER TABLE `employees` ADD UNIQUE KEY `employees_nik_unique` (`nik`)');
            }
        } catch (\Exception $e) {
            // Ignore if already exists or error
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove unique constraints
        try {
            DB::statement('ALTER TABLE `employees` DROP INDEX `employees_employee_code_unique`');
        } catch (\Exception $e) {
            // Ignore if doesn't exist
        }

        try {
            DB::statement('ALTER TABLE `employees` DROP INDEX `employees_email_unique`');
        } catch (\Exception $e) {
            // Ignore if doesn't exist
        }

        try {
            DB::statement('ALTER TABLE `employees` DROP INDEX `employees_nik_unique`');
        } catch (\Exception $e) {
            // Ignore if doesn't exist
        }
    }
}

