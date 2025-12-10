<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveStartEndTimeFromOvertimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overtimes', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('overtimes', 'start_time')) {
                $table->dropColumn('start_time');
            }
            if (Schema::hasColumn('overtimes', 'end_time')) {
                $table->dropColumn('end_time');
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
            // Add back columns if needed for rollback
            $table->string('start_time')->nullable()->after('overtime_date');
            $table->string('end_time')->nullable()->after('start_time');
        });
    }
}

