<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RenameIdToIdOvertimeInOvertimesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      // Rename primary key column from 'id' to 'id_overtime'
      DB::statement('ALTER TABLE `overtimes` CHANGE `id` `id_overtime` INT UNSIGNED NOT NULL AUTO_INCREMENT');
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      // Rename back to 'id'
      DB::statement('ALTER TABLE `overtimes` CHANGE `id_overtime` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT');
   }
}
