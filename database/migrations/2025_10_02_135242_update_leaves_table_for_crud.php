<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLeavesTableForCrud extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Change type column from tinyint to varchar
            $table->string('type', 50)->change();
            
            // Change status column from tinyint to varchar
            $table->string('status', 20)->change();
            
            // Change state column from tinyint to text
            $table->text('state')->nullable()->change();
            
            // Change leave_time from time to datetime
            $table->datetime('leave_time')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Revert changes
            $table->tinyint('type')->change();
            $table->tinyint('status')->change();
            $table->tinyint('state')->change();
            $table->time('leave_time')->change();
        });
    }
}