<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddForeignKeysToEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add new columns for foreign keys (nullable for backward compatibility)
            if (!Schema::hasColumn('employees', 'position_id')) {
                $table->unsignedInteger('position_id')->nullable()->after('position');
            }
            if (!Schema::hasColumn('employees', 'pendidikan_id')) {
                $table->unsignedInteger('pendidikan_id')->nullable()->after('pendidikan');
            }
            if (!Schema::hasColumn('employees', 'contract_type_id')) {
                $table->unsignedInteger('contract_type_id')->nullable()->after('kontrak_kerja');
            }
        });

        // Add foreign key constraints using raw SQL (to handle existing data)
        DB::statement('ALTER TABLE `employees` 
            ADD CONSTRAINT `employees_section_id_foreign` 
            FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL');

        DB::statement('ALTER TABLE `employees` 
            ADD CONSTRAINT `employees_wadir_id_foreign` 
            FOREIGN KEY (`wadir_id`) REFERENCES `wadirs` (`id`) ON DELETE SET NULL');

        // Add foreign keys for new lookup tables
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('set null');
            $table->foreign('pendidikan_id')->references('id')->on('pendidikan_levels')->onDelete('set null');
            $table->foreign('contract_type_id')->references('id')->on('contract_types')->onDelete('set null');
        });

        // Migrate existing data to lookup tables and update employees
        $this->migrateExistingData();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop foreign keys
            $table->dropForeign(['position_id']);
            $table->dropForeign(['pendidikan_id']);
            $table->dropForeign(['contract_type_id']);
            $table->dropForeign(['section_id']);
            $table->dropForeign(['wadir_id']);
            
            // Drop columns
            $table->dropColumn(['position_id', 'pendidikan_id', 'contract_type_id']);
        });
    }

    /**
     * Migrate existing data from employees to lookup tables
     */
    private function migrateExistingData()
    {
        // Migrate positions
        $positions = DB::table('employees')
            ->select('position')
            ->whereNotNull('position')
            ->distinct()
            ->pluck('position')
            ->filter();

        foreach ($positions as $position) {
            DB::table('positions')->insertOrIgnore([
                'name' => $position,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update employees.position_id based on positions.name
        $positionMap = DB::table('positions')->pluck('id', 'name');
        foreach ($positionMap as $name => $id) {
            DB::table('employees')
                ->where('position', $name)
                ->update(['position_id' => $id]);
        }

        // Migrate pendidikan
        $pendidikanList = DB::table('employees')
            ->select('pendidikan')
            ->whereNotNull('pendidikan')
            ->distinct()
            ->pluck('pendidikan')
            ->filter();

        $levelOrder = ['SD' => 1, 'SMP' => 2, 'SMA/SMK' => 3, 'D1' => 4, 'D2' => 5, 'D3' => 6, 'D4' => 7, 'S1' => 8, 'S2' => 9, 'S3' => 10];

        foreach ($pendidikanList as $pendidikan) {
            DB::table('pendidikan_levels')->insertOrIgnore([
                'name' => $pendidikan,
                'level_order' => $levelOrder[$pendidikan] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update employees.pendidikan_id
        $pendidikanMap = DB::table('pendidikan_levels')->pluck('id', 'name');
        foreach ($pendidikanMap as $name => $id) {
            DB::table('employees')
                ->where('pendidikan', $name)
                ->update(['pendidikan_id' => $id]);
        }

        // Migrate contract types
        $contractTypes = DB::table('employees')
            ->select('kontrak_kerja')
            ->whereNotNull('kontrak_kerja')
            ->distinct()
            ->pluck('kontrak_kerja')
            ->filter();

        foreach ($contractTypes as $type) {
            DB::table('contract_types')->insertOrIgnore([
                'name' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update employees.contract_type_id
        $contractMap = DB::table('contract_types')->pluck('id', 'name');
        foreach ($contractMap as $name => $id) {
            DB::table('employees')
                ->where('kontrak_kerja', $name)
                ->update(['contract_type_id' => $id]);
        }
    }
}
