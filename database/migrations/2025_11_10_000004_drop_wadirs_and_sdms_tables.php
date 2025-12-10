<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DropWadirsAndSdmsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop foreign keys from employees table first
        try {
            DB::statement('ALTER TABLE `employees` DROP FOREIGN KEY `employees_id_wadir_foreign`');
        } catch (\Exception $e) {
            // Ignore if doesn't exist
        }

        try {
            DB::statement('ALTER TABLE `employees` DROP FOREIGN KEY `employees_id_sdm_foreign`');
        } catch (\Exception $e) {
            // Ignore if doesn't exist
        }

        // Drop foreign keys from overtimes table
        try {
            DB::statement('ALTER TABLE `overtimes` DROP FOREIGN KEY `overtimes_id_wadir_foreign`');
        } catch (\Exception $e) {
            // Ignore if doesn't exist
        }

        try {
            DB::statement('ALTER TABLE `overtimes` DROP FOREIGN KEY `overtimes_id_sdm_foreign`');
        } catch (\Exception $e) {
            // Ignore if doesn't exist
        }

        // Drop columns from employees (old foreign keys)
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'id_wadir')) {
                $table->dropColumn('id_wadir');
            }
            if (Schema::hasColumn('employees', 'id_sdm')) {
                $table->dropColumn('id_sdm');
            }
        });

        // Drop columns from overtimes (old foreign keys)
        Schema::table('overtimes', function (Blueprint $table) {
            if (Schema::hasColumn('overtimes', 'id_wadir')) {
                $table->dropColumn('id_wadir');
            }
            if (Schema::hasColumn('overtimes', 'id_sdm')) {
                $table->dropColumn('id_sdm');
            }
        });

        // Drop tables
        Schema::dropIfExists('wadirs');
        Schema::dropIfExists('sdms');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Recreate wadirs table
        Schema::create('wadirs', function (Blueprint $table) {
            $table->increments('id_wadir');
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Recreate sdms table
        Schema::create('sdms', function (Blueprint $table) {
            $table->increments('id_sdm');
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Re-add columns to employees
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedInteger('id_wadir')->nullable()->after('id_section');
            $table->unsignedInteger('id_sdm')->nullable()->after('id_wadir');
        });

        // Re-add columns to overtimes
        Schema::table('overtimes', function (Blueprint $table) {
            $table->unsignedInteger('id_wadir')->nullable()->after('id_section');
            $table->unsignedInteger('id_sdm')->nullable()->after('id_wadir');
        });

        // Re-add foreign keys
        try {
            DB::statement('ALTER TABLE `employees` 
                    ADD CONSTRAINT `employees_id_wadir_foreign` 
                    FOREIGN KEY (`id_wadir`) REFERENCES `wadirs` (`id_wadir`) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // Ignore
        }

        try {
            DB::statement('ALTER TABLE `employees` 
                    ADD CONSTRAINT `employees_id_sdm_foreign` 
                    FOREIGN KEY (`id_sdm`) REFERENCES `sdms` (`id_sdm`) ON DELETE SET NULL');
        } catch (\Exception $e) {
            // Ignore
        }
    }
}

