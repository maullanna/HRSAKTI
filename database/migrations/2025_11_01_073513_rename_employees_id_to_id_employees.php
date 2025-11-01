<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RenameEmployeesIdToIdEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop all foreign key constraints that reference employees.id
        $this->dropForeignKeys();
        
        // Rename the id column to id_employees
        DB::statement('ALTER TABLE `employees` CHANGE `id` `id_employees` INT UNSIGNED NOT NULL AUTO_INCREMENT');
        
        // Recreate all foreign key constraints with new column name (id_employees)
        $this->createForeignKeys('id_employees');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop all foreign key constraints
        $this->dropForeignKeys();
        
        // Rename back to id
        DB::statement('ALTER TABLE `employees` CHANGE `id_employees` `id` INT UNSIGNED NOT NULL AUTO_INCREMENT');
        
        // Recreate all foreign key constraints with old column name (id)
        $this->createForeignKeys('id');
    }

    /**
     * Drop all foreign key constraints that reference employees table
     */
    private function dropForeignKeys()
    {
        $tables = [
            'attendances' => 'emp_id',
            'overtimes' => 'emp_id',
            'leaves' => 'emp_id',
            'salaries' => 'employee_id',
            'trainings' => 'employee_id',
        ];

        foreach ($tables as $table => $column) {
            if (Schema::hasTable($table)) {
                try {
                    // Get constraint name
                    $constraints = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = ? 
                        AND COLUMN_NAME = ? 
                        AND REFERENCED_TABLE_NAME = 'employees'
                    ", [$table, $column]);

                    foreach ($constraints as $constraint) {
                        DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
                    }
                } catch (\Exception $e) {
                    // Constraint might not exist, continue
                }
            }
        }

        // Drop foreign key for latetimes if exists
        if (Schema::hasTable('latetimes')) {
            try {
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'latetimes' 
                    AND COLUMN_NAME = 'emp_id' 
                    AND REFERENCED_TABLE_NAME = 'employees'
                ");

                foreach ($constraints as $constraint) {
                    DB::statement("ALTER TABLE `latetimes` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
                }
            } catch (\Exception $e) {
                // Constraint might not exist
            }
        }

        // Drop foreign key for schedule_employees if exists (pivot table)
        if (Schema::hasTable('schedule_employees')) {
            try {
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'schedule_employees' 
                    AND COLUMN_NAME = 'emp_id' 
                    AND REFERENCED_TABLE_NAME = 'employees'
                ");

                foreach ($constraints as $constraint) {
                    DB::statement("ALTER TABLE `schedule_employees` DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
                }
            } catch (\Exception $e) {
                // Constraint might not exist
            }
        }
    }

    /**
     * Create all foreign key constraints
     * 
     * @param string $referencedColumn Column name to reference (id_employees for up, id for down)
     */
    private function createForeignKeys($referencedColumn = 'id_employees')
    {
        // Recreate foreign keys with specified column name
        if (Schema::hasTable('attendances')) {
            try {
                Schema::table('attendances', function (Blueprint $table) use ($referencedColumn) {
                    $table->foreign('emp_id')->references($referencedColumn)->on('employees')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Might already exist
            }
        }

        if (Schema::hasTable('overtimes')) {
            try {
                Schema::table('overtimes', function (Blueprint $table) use ($referencedColumn) {
                    $table->foreign('emp_id')->references($referencedColumn)->on('employees')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Might already exist
            }
        }

        if (Schema::hasTable('leaves')) {
            try {
                Schema::table('leaves', function (Blueprint $table) use ($referencedColumn) {
                    $table->foreign('emp_id')->references($referencedColumn)->on('employees')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Might already exist
            }
        }

        if (Schema::hasTable('salaries')) {
            try {
                Schema::table('salaries', function (Blueprint $table) use ($referencedColumn) {
                    $table->foreign('employee_id')->references($referencedColumn)->on('employees')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Might already exist
            }
        }

        if (Schema::hasTable('trainings')) {
            try {
                Schema::table('trainings', function (Blueprint $table) use ($referencedColumn) {
                    $table->foreign('employee_id')->references($referencedColumn)->on('employees')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Might already exist
            }
        }

        // Recreate foreign key for latetimes if table exists
        if (Schema::hasTable('latetimes')) {
            try {
                Schema::table('latetimes', function (Blueprint $table) use ($referencedColumn) {
                    $table->foreign('emp_id')->references($referencedColumn)->on('employees')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Might already exist
            }
        }

        // Recreate foreign key for schedule_employees if table exists
        if (Schema::hasTable('schedule_employees')) {
            try {
                Schema::table('schedule_employees', function (Blueprint $table) use ($referencedColumn) {
                    $table->foreign('emp_id')->references($referencedColumn)->on('employees')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Might already exist
            }
        }
    }
}
