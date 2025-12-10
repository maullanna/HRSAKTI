<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RenameIdToSpecificNamesInAllTables extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      // Rename primary keys for all tables
      $tables = [
         'sections' => 'id_section',
         'wadirs' => 'id_wadir',
         'leaves' => 'id_leave',
         'attendances' => 'id_attendance',
         'latetimes' => 'id_latetime',
         'trainings' => 'id_training',
         'salaries' => 'id_salary',
         'settings' => 'id_setting',
         'schedules' => 'id_schedule',
         'roles' => 'id_role',
      ];

      foreach ($tables as $table => $newPrimaryKey) {
         if (Schema::hasTable($table)) {
            try {
               // Get current primary key type
               $columnInfo = DB::select("SHOW COLUMNS FROM `{$table}` WHERE `Key` = 'PRI'");

               if (!empty($columnInfo)) {
                  $currentColumn = $columnInfo[0];
                  $columnType = $currentColumn->Type;
                  $isAutoIncrement = strpos($currentColumn->Extra, 'auto_increment') !== false;

                  $autoIncrement = $isAutoIncrement ? 'AUTO_INCREMENT' : '';

                  // Rename primary key column
                  DB::statement("ALTER TABLE `{$table}` CHANGE `id` `{$newPrimaryKey}` {$columnType} NOT NULL {$autoIncrement}");
               }
            } catch (\Exception $e) {
               // Skip if column doesn't exist or already renamed
               if (
                  strpos($e->getMessage(), "doesn't exist") === false &&
                  strpos($e->getMessage(), "Duplicate column name") === false
               ) {
                  throw $e;
               }
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
      // Rename back to 'id'
      $tables = [
         'sections' => 'id_section',
         'wadirs' => 'id_wadir',
         'leaves' => 'id_leave',
         'attendances' => 'id_attendance',
         'latetimes' => 'id_latetime',
         'trainings' => 'id_training',
         'salaries' => 'id_salary',
         'settings' => 'id_setting',
         'schedules' => 'id_schedule',
         'roles' => 'id_role',
      ];

      foreach ($tables as $table => $oldPrimaryKey) {
         if (Schema::hasTable($table) && Schema::hasColumn($table, $oldPrimaryKey)) {
            try {
               $columnInfo = DB::select("SHOW COLUMNS FROM `{$table}` WHERE `Field` = '{$oldPrimaryKey}'");

               if (!empty($columnInfo)) {
                  $currentColumn = $columnInfo[0];
                  $columnType = $currentColumn->Type;
                  $isAutoIncrement = strpos($currentColumn->Extra, 'auto_increment') !== false;

                  $autoIncrement = $isAutoIncrement ? 'AUTO_INCREMENT' : '';

                  DB::statement("ALTER TABLE `{$table}` CHANGE `{$oldPrimaryKey}` `id` {$columnType} NOT NULL {$autoIncrement}");
               }
            } catch (\Exception $e) {
               // Ignore errors
            }
         }
      }
   }
}
