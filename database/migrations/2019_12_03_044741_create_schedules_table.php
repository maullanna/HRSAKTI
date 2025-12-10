<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('schedules', function (Blueprint $table) {
         $table->increments('id');
         $table->time('time_in');
         $table->time('time_out');
         $table->string('name')->nullable();
         $table->text('description')->nullable();
         $table->timestamps();
      });

      // Create pivot table for schedule_employees
      Schema::create('schedule_employees', function (Blueprint $table) {
         $table->integer('emp_id')->unsigned();
         $table->integer('schedule_id')->unsigned();
         $table->timestamps();

         // Foreign keys - using id_employees to match Employee model primary key
         $table->foreign('emp_id')->references('id_employees')->on('employees')->onDelete('cascade');
         $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');

         // Primary key on pivot table
         $table->primary(['emp_id', 'schedule_id']);
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::table('schedule_employees', function (Blueprint $table) {
         $table->dropForeign(['emp_id']);
         $table->dropForeign(['schedule_id']);
      });

      Schema::dropIfExists('schedule_employees');
      Schema::dropIfExists('schedules');
   }
}
