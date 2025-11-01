<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NormalizeLeaveTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create leave_types table
        if (!Schema::hasTable('leave_types')) {
            Schema::create('leave_types', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50)->unique();
                $table->integer('max_days')->nullable();
                $table->boolean('requires_approval')->default(true);
                $table->timestamps();
            });
        }

        // Create leave_statuses table
        if (!Schema::hasTable('leave_statuses')) {
            Schema::create('leave_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name', 20)->unique();
                $table->timestamps();
            });
        }

        // Add new columns to leaves table
        Schema::table('leaves', function (Blueprint $table) {
            if (!Schema::hasColumn('leaves', 'status_id')) {
                $table->unsignedInteger('status_id')->nullable()->after('status');
            }
            if (!Schema::hasColumn('leaves', 'type_id')) {
                $table->unsignedInteger('type_id')->nullable()->after('type');
            }
        });

        // Add foreign keys
        Schema::table('leaves', function (Blueprint $table) {
            $table->foreign('status_id')->references('id')->on('leave_statuses')->onDelete('set null');
            $table->foreign('type_id')->references('id')->on('leave_types')->onDelete('set null');
        });

        // Migrate existing data
        $this->migrateLeaveData();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropForeign(['type_id']);
            $table->dropColumn(['status_id', 'type_id']);
        });

        Schema::dropIfExists('leave_statuses');
        Schema::dropIfExists('leave_types');
    }

    /**
     * Migrate existing leave data to lookup tables
     */
    private function migrateLeaveData()
    {
        // Migrate leave types
        $leaveTypes = DB::table('leaves')
            ->select('type')
            ->whereNotNull('type')
            ->distinct()
            ->pluck('type')
            ->filter();

        $defaultMaxDays = [
            'sick' => 30,
            'vacation' => 12,
            'personal' => 5,
            'emergency' => 3,
        ];

        foreach ($leaveTypes as $type) {
            DB::table('leave_types')->insertOrIgnore([
                'name' => $type,
                'max_days' => $defaultMaxDays[$type] ?? null,
                'requires_approval' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update leaves.type_id
        $typeMap = DB::table('leave_types')->pluck('id', 'name');
        foreach ($typeMap as $name => $id) {
            DB::table('leaves')
                ->where('type', $name)
                ->update(['type_id' => $id]);
        }

        // Migrate leave statuses
        $statuses = DB::table('leaves')
            ->select('status')
            ->whereNotNull('status')
            ->distinct()
            ->pluck('status')
            ->filter();

        foreach ($statuses as $status) {
            DB::table('leave_statuses')->insertOrIgnore([
                'name' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update leaves.status_id
        $statusMap = DB::table('leave_statuses')->pluck('id', 'name');
        foreach ($statusMap as $name => $id) {
            DB::table('leaves')
                ->where('status', $name)
                ->update(['status_id' => $id]);
        }
    }
}
