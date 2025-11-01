<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveEmploymentContractFromEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('employees', 'employment_contract')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('employment_contract');
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
        if (!Schema::hasColumn('employees', 'employment_contract')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('employment_contract', 100)->nullable()->after('kontrak_kerja');
            });
        }
    }
}
