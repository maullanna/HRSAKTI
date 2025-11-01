<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveBasicSalaryAndAddEmploymentContractToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove basic_salary column if exists
        if (Schema::hasColumn('employees', 'basic_salary')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('basic_salary');
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
        // Restore basic_salary column if doesn't exist
        if (!Schema::hasColumn('employees', 'basic_salary')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->decimal('basic_salary', 10, 2)->nullable()->after('phone');
            });
        }
    }
}
