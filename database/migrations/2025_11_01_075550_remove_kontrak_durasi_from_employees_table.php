<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveKontrakDurasiFromEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('employees', 'kontrak_durasi')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('kontrak_durasi');
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
        if (!Schema::hasColumn('employees', 'kontrak_durasi')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->integer('kontrak_durasi')->nullable()->after('kontrak_kerja')->comment('Durasi Kontrak dalam bulan');
            });
        }
    }
}
