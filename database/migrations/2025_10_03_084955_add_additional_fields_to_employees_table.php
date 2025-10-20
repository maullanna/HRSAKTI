<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->after('employee_code')->comment('Nomor Induk Kependudukan');
            $table->date('tanggal_lahir')->nullable()->after('nik')->comment('Tanggal Lahir');
            $table->string('pendidikan', 100)->nullable()->after('tanggal_lahir')->comment('Pendidikan Terakhir');
            $table->string('kontrak_kerja', 50)->nullable()->after('pendidikan')->comment('Status Kontrak Kerja');
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
            $table->dropColumn(['nik', 'tanggal_lahir', 'pendidikan', 'kontrak_kerja']);
        });
    }
}
