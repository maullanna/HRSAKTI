<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Index untuk attendance_date (sering digunakan di WHERE dan ORDER BY)
            if (!$this->indexExists('attendances', 'idx_attendance_date')) {
                $table->index('attendance_date', 'idx_attendance_date');
            }
            
            // Composite index untuk query yang sering digunakan bersama (emp_id + date)
            if (!$this->indexExists('attendances', 'idx_emp_date')) {
                $table->index(['emp_id', 'attendance_date'], 'idx_emp_date');
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
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('idx_attendance_date');
            $table->dropIndex('idx_emp_date');
        });
    }

    /**
     * Check if index already exists
     */
    private function indexExists($table, $index)
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        $indexes = $connection->select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$index]);
        return count($indexes) > 0;
    }
}
