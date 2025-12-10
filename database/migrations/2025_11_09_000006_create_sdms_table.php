<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSdmsTable extends Migration
{
   /**
    * Run the migrations.
    *
    * @return void
    */
   public function up()
   {
      Schema::create('sdms', function (Blueprint $table) {
         $table->increments('id_sdm');
         $table->string('name');
         $table->string('code', 20)->unique();
         $table->text('description')->nullable();
         $table->boolean('is_active')->default(true);
         $table->timestamps();
      });
   }

   /**
    * Reverse the migrations.
    *
    * @return void
    */
   public function down()
   {
      Schema::dropIfExists('sdms');
   }
}
