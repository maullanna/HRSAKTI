<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractTrackingFieldsToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'kontrak_durasi')) {
                $table->integer('kontrak_durasi')->nullable()->after('kontrak_kerja')
                    ->comment('Durasi kontrak dalam bulan (untuk Magang, Kontrak, PKL, Freelance)');
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
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'kontrak_durasi')) {
                $table->dropColumn('kontrak_durasi');
            }
        });
    }
}
