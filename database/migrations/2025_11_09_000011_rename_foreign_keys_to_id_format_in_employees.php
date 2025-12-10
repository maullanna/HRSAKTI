<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RenameForeignKeysToIdFormatInEmployees extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      // Rename section_id to id_section
      if (Schema::hasColumn('employees', 'section_id')) {
         try {
            // Drop foreign key first
            DB::statement('ALTER TABLE `employees` DROP FOREIGN KEY `employees_section_id_foreign`');
         } catch (\Exception $e) {
            // Ignore if doesn't exist
         }

         // Rename column
         DB::statement('ALTER TABLE `employees` CHANGE `section_id` `id_section` INT UNSIGNED NULL');

         // Re-add foreign key with new name
         try {
            DB::statement('ALTER TABLE `employees` 
                    ADD CONSTRAINT `employees_id_section_foreign` 
                    FOREIGN KEY (`id_section`) REFERENCES `sections` (`id_section`) ON DELETE SET NULL');
         } catch (\Exception $e) {
            // Ignore if already exists
         }
      }

      // Rename wadir_id to id_wadir
      if (Schema::hasColumn('employees', 'wadir_id')) {
         try {
            DB::statement('ALTER TABLE `employees` DROP FOREIGN KEY `employees_wadir_id_foreign`');
         } catch (\Exception $e) {
            // Ignore
         }

         DB::statement('ALTER TABLE `employees` CHANGE `wadir_id` `id_wadir` INT UNSIGNED NULL');

         try {
            DB::statement('ALTER TABLE `employees` 
                    ADD CONSTRAINT `employees_id_wadir_foreign` 
                    FOREIGN KEY (`id_wadir`) REFERENCES `wadirs` (`id_wadir`) ON DELETE SET NULL');
         } catch (\Exception $e) {
            // Ignore
         }
      }

      // Rename sdm_id to id_sdm
      if (Schema::hasColumn('employees', 'sdm_id')) {
         try {
            DB::statement('ALTER TABLE `employees` DROP FOREIGN KEY `employees_sdm_id_foreign`');
         } catch (\Exception $e) {
            // Ignore
         }

         DB::statement('ALTER TABLE `employees` CHANGE `sdm_id` `id_sdm` INT UNSIGNED NULL');

         try {
            DB::statement('ALTER TABLE `employees` 
                    ADD CONSTRAINT `employees_id_sdm_foreign` 
                    FOREIGN KEY (`id_sdm`) REFERENCES `sdms` (`id_sdm`) ON DELETE SET NULL');
         } catch (\Exception $e) {
            // Ignore
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
      // Revert id_section to section_id
      if (Schema::hasColumn('employees', 'id_section')) {
         try {
            DB::statement('ALTER TABLE `employees` DROP FOREIGN KEY `employees_id_section_foreign`');
         } catch (\Exception $e) {
         }

         DB::statement('ALTER TABLE `employees` CHANGE `id_section` `section_id` INT UNSIGNED NULL');

         try {
            DB::statement('ALTER TABLE `employees` 
                    ADD CONSTRAINT `employees_section_id_foreign` 
                    FOREIGN KEY (`section_id`) REFERENCES `sections` (`id_section`) ON DELETE SET NULL');
         } catch (\Exception $e) {
         }
      }

      // Revert id_wadir to wadir_id
      if (Schema::hasColumn('employees', 'id_wadir')) {
         try {
            DB::statement('ALTER TABLE `employees` DROP FOREIGN KEY `employees_id_wadir_foreign`');
         } catch (\Exception $e) {
         }

         DB::statement('ALTER TABLE `employees` CHANGE `id_wadir` `wadir_id` INT UNSIGNED NULL');

         try {
            DB::statement('ALTER TABLE `employees` 
                    ADD CONSTRAINT `employees_wadir_id_foreign` 
                    FOREIGN KEY (`wadir_id`) REFERENCES `wadirs` (`id_wadir`) ON DELETE SET NULL');
         } catch (\Exception $e) {
         }
      }

      // Revert id_sdm to sdm_id
      if (Schema::hasColumn('employees', 'id_sdm')) {
         try {
            DB::statement('ALTER TABLE `employees` DROP FOREIGN KEY `employees_id_sdm_foreign`');
         } catch (\Exception $e) {
         }

         DB::statement('ALTER TABLE `employees` CHANGE `id_sdm` `sdm_id` INT UNSIGNED NULL');

         try {
            DB::statement('ALTER TABLE `employees` 
                    ADD CONSTRAINT `employees_sdm_id_foreign` 
                    FOREIGN KEY (`sdm_id`) REFERENCES `sdms` (`id_sdm`) ON DELETE SET NULL');
         } catch (\Exception $e) {
         }
      }
   }
}
