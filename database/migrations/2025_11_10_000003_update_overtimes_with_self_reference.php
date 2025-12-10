<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateOvertimesWithSelfReference extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overtimes', function (Blueprint $table) {
            // Add self-reference fields
            if (!Schema::hasColumn('overtimes', 'id_section_employee')) {
                $table->unsignedInteger('id_section_employee')->nullable()->after('id_section');
            }
            if (!Schema::hasColumn('overtimes', 'id_wadir_employee')) {
                $table->unsignedInteger('id_wadir_employee')->nullable()->after('id_section_employee');
            }
            if (!Schema::hasColumn('overtimes', 'id_sdm_employee')) {
                $table->unsignedInteger('id_sdm_employee')->nullable()->after('id_wadir_employee');
            }
            if (!Schema::hasColumn('overtimes', 'id_director_employee')) {
                $table->unsignedInteger('id_director_employee')->nullable()->after('id_sdm_employee');
            }
        });

        // Add foreign key constraints for self-reference
        try {
            DB::statement('ALTER TABLE `overtimes` 
                    ADD CONSTRAINT `overtimes_id_section_employee_foreign` 
                    FOREIGN KEY (`id_section_employee`) REFERENCES `employees` (`id_employees`) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // Ignore if already exists
        }

        try {
            DB::statement('ALTER TABLE `overtimes` 
                    ADD CONSTRAINT `overtimes_id_wadir_employee_foreign` 
                    FOREIGN KEY (`id_wadir_employee`) REFERENCES `employees` (`id_employees`) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // Ignore if already exists
        }

        try {
            DB::statement('ALTER TABLE `overtimes` 
                    ADD CONSTRAINT `overtimes_id_sdm_employee_foreign` 
                    FOREIGN KEY (`id_sdm_employee`) REFERENCES `employees` (`id_employees`) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // Ignore if already exists
        }

        try {
            DB::statement('ALTER TABLE `overtimes` 
                    ADD CONSTRAINT `overtimes_id_director_employee_foreign` 
                    FOREIGN KEY (`id_director_employee`) REFERENCES `employees` (`id_employees`) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // Ignore if already exists
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop foreign keys first
        try {
            DB::statement('ALTER TABLE `overtimes` DROP FOREIGN KEY `overtimes_id_section_employee_foreign`');
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::statement('ALTER TABLE `overtimes` DROP FOREIGN KEY `overtimes_id_wadir_employee_foreign`');
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::statement('ALTER TABLE `overtimes` DROP FOREIGN KEY `overtimes_id_sdm_employee_foreign`');
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::statement('ALTER TABLE `overtimes` DROP FOREIGN KEY `overtimes_id_director_employee_foreign`');
        } catch (\Exception $e) {
            // Ignore
        }

        // Drop columns
        Schema::table('overtimes', function (Blueprint $table) {
            if (Schema::hasColumn('overtimes', 'id_director_employee')) {
                $table->dropColumn('id_director_employee');
            }
            if (Schema::hasColumn('overtimes', 'id_sdm_employee')) {
                $table->dropColumn('id_sdm_employee');
            }
            if (Schema::hasColumn('overtimes', 'id_wadir_employee')) {
                $table->dropColumn('id_wadir_employee');
            }
            if (Schema::hasColumn('overtimes', 'id_section_employee')) {
                $table->dropColumn('id_section_employee');
            }
        });
    }
}

