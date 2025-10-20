<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOvertimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overtimes', function (Blueprint $table) {
            // Add approval system fields
            $table->text('reason')->nullable()->after('overtime_date');
            $table->enum('status', ['pending', 'plotted', 'approved_section', 'approved_wadir', 'rejected'])
                  ->default('pending')->after('reason');
            
            // Add plotting fields
            $table->integer('plotted_by')->unsigned()->nullable()->after('status');
            $table->timestamp('plotted_at')->nullable()->after('plotted_by');
            $table->string('project_name')->nullable()->after('plotted_at');
            $table->text('task_description')->nullable()->after('project_name');
            
            // Add approval fields
            $table->integer('approved_by_section')->unsigned()->nullable()->after('task_description');
            $table->integer('approved_by_wadir')->unsigned()->nullable()->after('approved_by_section');
            $table->timestamp('approved_section_at')->nullable()->after('approved_by_wadir');
            $table->timestamp('approved_wadir_at')->nullable()->after('approved_section_at');
            
            // Add time fields
            $table->time('start_time')->nullable()->after('approved_wadir_at');
            $table->time('end_time')->nullable()->after('start_time');
            
            // Add foreign key constraints
            $table->foreign('plotted_by')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('approved_by_section')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('approved_by_wadir')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('overtimes', function (Blueprint $table) {
            $table->dropForeign(['plotted_by']);
            $table->dropForeign(['approved_by_section']);
            $table->dropForeign(['approved_by_wadir']);
            
            $table->dropColumn([
                'reason', 'status', 'plotted_by', 'plotted_at', 'project_name',
                'task_description', 'approved_by_section', 'approved_by_wadir',
                'approved_section_at', 'approved_wadir_at', 'start_time', 'end_time'
            ]);
        });
    }
}

