<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if columns exist and add them if they don't
        $columns = Schema::getColumnListing('employees');
        
        if (!in_array('role_id', $columns)) {
            Schema::table('employees', function (Blueprint $table) {
                $table->integer('role_id')->unsigned()->nullable()->after('id');
            });
        }
        
        if (!in_array('section_id', $columns)) {
            Schema::table('employees', function (Blueprint $table) {
                $table->integer('section_id')->unsigned()->nullable()->after('role_id');
            });
        }
        
        if (!in_array('wadir_id', $columns)) {
            Schema::table('employees', function (Blueprint $table) {
                $table->integer('wadir_id')->unsigned()->nullable()->after('section_id');
            });
        }
        
        if (!in_array('employee_code', $columns)) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('employee_code', 20)->unique()->after('name');
            });
        }
        
        if (!in_array('phone', $columns)) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('phone', 20)->nullable()->after('email');
            });
        }
        
        if (!in_array('address', $columns)) {
            Schema::table('employees', function (Blueprint $table) {
                $table->text('address')->nullable()->after('phone');
            });
        }
        
        if (!in_array('basic_salary', $columns)) {
            Schema::table('employees', function (Blueprint $table) {
                $table->decimal('basic_salary', 10, 2)->nullable()->after('address');
            });
        }
        
        if (!in_array('hire_date', $columns)) {
            Schema::table('employees', function (Blueprint $table) {
                $table->date('hire_date')->nullable()->after('basic_salary');
            });
        }
        
        if (!in_array('status', $columns)) {
            Schema::table('employees', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('hire_date');
            });
        }

        // Add foreign key constraints if they don't exist
        try {
            if (in_array('role_id', $columns)) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
                });
            }
        } catch (Exception $e) {
            // Foreign key already exists
        }
        
        try {
            if (in_array('section_id', $columns)) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');
                });
            }
        } catch (Exception $e) {
            // Foreign key already exists
        }
        
        try {
            if (in_array('wadir_id', $columns)) {
                Schema::table('employees', function (Blueprint $table) {
                    $table->foreign('wadir_id')->references('id')->on('wadirs')->onDelete('set null');
                });
            }
        } catch (Exception $e) {
            // Foreign key already exists
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
            $table->dropForeign(['role_id']);
            $table->dropForeign(['section_id']);
            $table->dropForeign(['wadir_id']);
            
            $table->dropColumn([
                'role_id', 'section_id', 'wadir_id', 'employee_code',
                'phone', 'address', 'basic_salary', 'hire_date', 'status'
            ]);
        });
    }
}
