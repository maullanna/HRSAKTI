<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveAddressFromEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('employees', 'address')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('address');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('employees', 'address')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->text('address')->nullable()->after('phone');
            });
        }
    }
}
