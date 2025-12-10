<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWadirApprovalToOvertimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overtimes', function (Blueprint $table) {
            // Add wadir approval fields
            if (!Schema::hasColumn('overtimes', 'wadir_approved')) {
                $table->boolean('wadir_approved')->default(false)->after('section_approved_at');
            }
            if (!Schema::hasColumn('overtimes', 'wadir_approved_by')) {
                $table->unsignedInteger('wadir_approved_by')->nullable()->after('wadir_approved');
            }
            if (!Schema::hasColumn('overtimes', 'wadir_approved_at')) {
                $table->timestamp('wadir_approved_at')->nullable()->after('wadir_approved_by');
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
        Schema::table('overtimes', function (Blueprint $table) {
            if (Schema::hasColumn('overtimes', 'wadir_approved_at')) {
                $table->dropColumn('wadir_approved_at');
            }
            if (Schema::hasColumn('overtimes', 'wadir_approved_by')) {
                $table->dropColumn('wadir_approved_by');
            }
            if (Schema::hasColumn('overtimes', 'wadir_approved')) {
                $table->dropColumn('wadir_approved');
            }
        });
    }
}

