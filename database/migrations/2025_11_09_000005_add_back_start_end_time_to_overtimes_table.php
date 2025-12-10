<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBackStartEndTimeToOvertimesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::table('overtimes', function (Blueprint $table) {
         // Add back start_time and end_time columns for reporting purposes
         if (!Schema::hasColumn('overtimes', 'start_time')) {
            $table->time('start_time')->nullable()->after('overtime_date');
         }
         if (!Schema::hasColumn('overtimes', 'end_time')) {
            $table->time('end_time')->nullable()->after('start_time');
         }
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::table('overtimes', function (Blueprint $table) {
         if (Schema::hasColumn('overtimes', 'start_time')) {
            $table->dropColumn('start_time');
         }
         if (Schema::hasColumn('overtimes', 'end_time')) {
            $table->dropColumn('end_time');
         }
      });
   }
}
