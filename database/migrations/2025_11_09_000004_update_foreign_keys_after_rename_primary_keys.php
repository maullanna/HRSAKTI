<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateForeignKeysAfterRenamePrimaryKeys extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      // Update foreign keys that reference renamed primary keys

      // Update employees.section_id to reference sections.id_section
      if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'section_id')) {
         try {
            // Drop existing foreign key
            DB::statement('ALTER TABLE `employees` DROP FOREIGN KEY `employees_section_id_foreign`');
         } catch (\Exception $e) {
            // Ignore if doesn't exist
         }

         try {
            // Add new foreign key
            DB::statement('ALTER TABLE `employees` 
                    ADD CONSTRAINT `employees_section_id_foreign` 
                    FOREIGN KEY (`section_id`) REFERENCES `sections` (`id_section`) ON DELETE SET NULL');
         } catch (\Exception $e) {
            // Ignore if already exists
         }
      }

      // Update employees.wadir_id to reference wadirs.id_wadir
      if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'wadir_id')) {
         try {
            DB::statement('ALTER TABLE `employees` DROP FOREIGN KEY `employees_wadir_id_foreign`');
         } catch (\Exception $e) {
            // Ignore
         }

         try {
            DB::statement('ALTER TABLE `employees` 
                    ADD CONSTRAINT `employees_wadir_id_foreign` 
                    FOREIGN KEY (`wadir_id`) REFERENCES `wadirs` (`id_wadir`) ON DELETE SET NULL');
         } catch (\Exception $e) {
            // Ignore
         }
      }

      // Update employees.role_id to reference roles.id_role
      if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'role_id')) {
         try {
            DB::statement('ALTER TABLE `employees` DROP FOREIGN KEY `employees_role_id_foreign`');
         } catch (\Exception $e) {
            // Ignore
         }

         try {
            DB::statement('ALTER TABLE `employees` 
                    ADD CONSTRAINT `employees_role_id_foreign` 
                    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id_role`) ON DELETE SET NULL');
         } catch (\Exception $e) {
            // Ignore
         }
      }

      // Update schedule_employees.schedule_id to reference schedules.id_schedule
      if (Schema::hasTable('schedule_employees') && Schema::hasColumn('schedule_employees', 'schedule_id')) {
         try {
            DB::statement('ALTER TABLE `schedule_employees` DROP FOREIGN KEY `schedule_employees_schedule_id_foreign`');
         } catch (\Exception $e) {
            // Ignore
         }

         try {
            DB::statement('ALTER TABLE `schedule_employees` 
                    ADD CONSTRAINT `schedule_employees_schedule_id_foreign` 
                    FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id_schedule`) ON DELETE CASCADE');
         } catch (\Exception $e) {
            // Ignore
         }
      }

      // Update role_users.role_id to reference roles.id_role
      if (Schema::hasTable('role_users') && Schema::hasColumn('role_users', 'role_id')) {
         try {
            DB::statement('ALTER TABLE `role_users` DROP FOREIGN KEY `role_users_role_id_foreign`');
         } catch (\Exception $e) {
            // Ignore
         }

         try {
            DB::statement('ALTER TABLE `role_users` 
                    ADD CONSTRAINT `role_users_role_id_foreign` 
                    FOREIGN KEY (`role_id`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE');
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
      // Revert foreign keys back to 'id'
      // Similar logic but reverse
   }
}
