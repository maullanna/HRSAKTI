<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Add modern leave system fields
            $table->enum('leave_type', ['annual', 'sick', 'personal', 'maternity', 'other'])
                  ->default('annual')->after('emp_id');
            $table->text('reason')->nullable()->after('leave_type');
            $table->date('start_date')->nullable()->after('reason');
            $table->date('end_date')->nullable()->after('start_date');
            $table->integer('days_taken')->default(1)->after('end_date');
            
            // Update status field
            $table->enum('status', ['pending', 'approved_section', 'approved_wadir', 'approved_admin', 'rejected'])
                  ->default('pending')->change();
            
            // Add approval fields
            $table->integer('approved_by_section')->unsigned()->nullable()->after('status');
            $table->integer('approved_by_wadir')->unsigned()->nullable()->after('approved_by_section');
            $table->integer('approved_by_admin')->unsigned()->nullable()->after('approved_by_wadir');
            $table->timestamp('approved_section_at')->nullable()->after('approved_by_admin');
            $table->timestamp('approved_wadir_at')->nullable()->after('approved_section_at');
            $table->timestamp('approved_admin_at')->nullable()->after('approved_wadir_at');
            
            // Add foreign key constraints
            $table->foreign('approved_by_section')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('approved_by_wadir')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('approved_by_admin')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['approved_by_section']);
            $table->dropForeign(['approved_by_wadir']);
            $table->dropForeign(['approved_by_admin']);
            
            $table->dropColumn([
                'leave_type', 'reason', 'start_date', 'end_date', 'days_taken',
                'approved_by_section', 'approved_by_wadir', 'approved_by_admin',
                'approved_section_at', 'approved_wadir_at', 'approved_admin_at'
            ]);
        });
    }
}

