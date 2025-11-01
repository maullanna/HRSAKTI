<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLookupTablesForNormalization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create positions table
        if (!Schema::hasTable('positions')) {
            Schema::create('positions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Create pendidikan_levels table
        if (!Schema::hasTable('pendidikan_levels')) {
            Schema::create('pendidikan_levels', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->unique();
                $table->integer('level_order')->nullable()->comment('Untuk sorting: 1=SD, 2=SMP, 3=SMA, dll');
                $table->timestamps();
            });
        }

        // Create contract_types table
        if (!Schema::hasTable('contract_types')) {
            Schema::create('contract_types', function (Blueprint $table) {
                $table->id();
                $table->string('name', 50)->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_types');
        Schema::dropIfExists('pendidikan_levels');
        Schema::dropIfExists('positions');
    }
}
