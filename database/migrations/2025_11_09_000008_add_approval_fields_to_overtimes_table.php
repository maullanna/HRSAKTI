<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddApprovalFieldsToOvertimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overtimes', function (Blueprint $table) {
            // Add section, wadir, and sdm foreign keys
            if (!Schema::hasColumn('overtimes', 'id_section')) {
                $table->unsignedInteger('id_section')->nullable()->after('emp_id');
            }
            if (!Schema::hasColumn('overtimes', 'id_wadir')) {
                $table->unsignedInteger('id_wadir')->nullable()->after('id_section');
            }
            if (!Schema::hasColumn('overtimes', 'id_sdm')) {
                $table->unsignedInteger('id_sdm')->nullable()->after('id_wadir');
            }
            
            // Add approval status fields
            if (!Schema::hasColumn('overtimes', 'section_approved')) {
                $table->boolean('section_approved')->default(false)->after('status');
            }
            if (!Schema::hasColumn('overtimes', 'section_approved_by')) {
                $table->unsignedInteger('section_approved_by')->nullable()->after('section_approved');
            }
            if (!Schema::hasColumn('overtimes', 'section_approved_at')) {
                $table->timestamp('section_approved_at')->nullable()->after('section_approved_by');
            }
            if (!Schema::hasColumn('overtimes', 'sdm_approved')) {
                $table->boolean('sdm_approved')->default(false)->after('section_approved_at');
            }
            if (!Schema::hasColumn('overtimes', 'sdm_approved_by')) {
                $table->unsignedInteger('sdm_approved_by')->nullable()->after('sdm_approved');
            }
            if (!Schema::hasColumn('overtimes', 'sdm_approved_at')) {
                $table->timestamp('sdm_approved_at')->nullable()->after('sdm_approved_by');
            }
        });

        // Add foreign key constraints
        if (Schema::hasTable('sections')) {
            try {
                DB::statement('ALTER TABLE `overtimes` 
                    ADD CONSTRAINT `overtimes_id_section_foreign` 
                    FOREIGN KEY (`id_section`) REFERENCES `sections` (`id_section`) ON DELETE SET NULL');
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }

        if (Schema::hasTable('wadirs')) {
            try {
                DB::statement('ALTER TABLE `overtimes` 
                    ADD CONSTRAINT `overtimes_id_wadir_foreign` 
                    FOREIGN KEY (`id_wadir`) REFERENCES `wadirs` (`id_wadir`) ON DELETE SET NULL');
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }

        if (Schema::hasTable('sdms')) {
            try {
                DB::statement('ALTER TABLE `overtimes` 
                    ADD CONSTRAINT `overtimes_id_sdm_foreign` 
                    FOREIGN KEY (`id_sdm`) REFERENCES `sdms` (`id_sdm`) ON DELETE SET NULL');
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
        Schema::table('overtimes', function (Blueprint $table) {
            // Drop foreign keys first
            if (Schema::hasColumn('overtimes', 'id_section')) {
                try {
                    $table->dropForeign(['id_section']);
                } catch (\Exception $e) {}
                $table->dropColumn('id_section');
            }
            if (Schema::hasColumn('overtimes', 'id_wadir')) {
                try {
                    $table->dropForeign(['id_wadir']);
                } catch (\Exception $e) {}
                $table->dropColumn('id_wadir');
            }
            if (Schema::hasColumn('overtimes', 'id_sdm')) {
                try {
                    $table->dropForeign(['id_sdm']);
                } catch (\Exception $e) {}
                $table->dropColumn('id_sdm');
            }
            
            // Drop approval fields
            $columns = ['section_approved', 'section_approved_by', 'section_approved_at', 
                       'sdm_approved', 'sdm_approved_by', 'sdm_approved_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('overtimes', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}

