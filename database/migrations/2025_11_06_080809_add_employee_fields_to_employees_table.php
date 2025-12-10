<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddEmployeeFieldsToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if columns already exist before adding
        if (!Schema::hasColumn('employees', 'nik')) {
            DB::statement('ALTER TABLE `employees` ADD COLUMN `nik` VARCHAR(16) NULL AFTER `employee_code`');
        }

        if (!Schema::hasColumn('employees', 'tanggal_lahir')) {
            DB::statement('ALTER TABLE `employees` ADD COLUMN `tanggal_lahir` DATE NULL AFTER `nik`');
        }

        if (!Schema::hasColumn('employees', 'pendidikan')) {
            DB::statement("ALTER TABLE `employees` ADD COLUMN `pendidikan` ENUM('SD','SMP','SMA/SMK','D1','D2','D3','D4','S1','S2','S3') NULL AFTER `tanggal_lahir`");
        }

        if (!Schema::hasColumn('employees', 'kontrak_kerja')) {
            DB::statement("ALTER TABLE `employees` ADD COLUMN `kontrak_kerja` ENUM('Tetap','Kontrak','Magang','PKL','Freelance') NULL DEFAULT 'Tetap' AFTER `pendidikan`");
        }

        if (!Schema::hasColumn('employees', 'kontrak_durasi')) {
            DB::statement('ALTER TABLE `employees` ADD COLUMN `kontrak_durasi` INT NULL AFTER `kontrak_kerja`');
        }
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
            if (Schema::hasColumn('employees', 'kontrak_kerja')) {
                $table->dropColumn('kontrak_kerja');
            }
            if (Schema::hasColumn('employees', 'pendidikan')) {
                $table->dropColumn('pendidikan');
            }
            if (Schema::hasColumn('employees', 'tanggal_lahir')) {
                $table->dropColumn('tanggal_lahir');
            }
            if (Schema::hasColumn('employees', 'nik')) {
                $table->dropColumn('nik');
            }
        });
    }
}
