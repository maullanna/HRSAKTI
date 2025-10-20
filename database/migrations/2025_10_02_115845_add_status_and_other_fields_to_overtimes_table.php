<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndOtherFieldsToOvertimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('overtimes', function (Blueprint $table) {
            // Add new columns
            $table->string('start_time')->nullable()->after('overtime_date');
            $table->string('end_time')->nullable()->after('start_time');
            $table->text('reason')->nullable()->after('end_time');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('reason');
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
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
            // Drop added columns
            $table->dropColumn([
                'start_time', 'end_time', 'reason', 'status', 'approved_by', 'approved_at'
            ]);
        });
    }
}
