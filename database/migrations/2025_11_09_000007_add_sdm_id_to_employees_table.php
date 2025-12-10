<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSdmIdToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'sdm_id')) {
                $table->unsignedInteger('sdm_id')->nullable()->after('wadir_id');
            }
        });

        // Add foreign key constraint
        if (Schema::hasTable('sdms')) {
            try {
                DB::statement('ALTER TABLE `employees` 
                    ADD CONSTRAINT `employees_sdm_id_foreign` 
                    FOREIGN KEY (`sdm_id`) REFERENCES `sdms` (`id_sdm`) ON DELETE SET NULL');
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
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
            if (Schema::hasColumn('employees', 'sdm_id')) {
                try {
                    $table->dropForeign(['sdm_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
                $table->dropColumn('sdm_id');
            }
        });
    }
}

