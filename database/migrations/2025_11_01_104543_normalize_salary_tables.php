<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class NormalizeSalaryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create salary_allowances table
        if (!Schema::hasTable('salary_allowances')) {
            Schema::create('salary_allowances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('salary_id');
                $table->string('type', 100)->comment('transport, meal, overtime, bonus, dll');
                $table->decimal('amount', 10, 2);
                $table->text('description')->nullable();
                $table->timestamps();

                $table->foreign('salary_id')
                    ->references('id')
                    ->on('salaries')
                    ->onDelete('cascade');
                
                $table->index('salary_id');
            });
        }

        // Create salary_deductions table
        if (!Schema::hasTable('salary_deductions')) {
            Schema::create('salary_deductions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('salary_id');
                $table->string('type', 100)->comment('tax, insurance, loan, penalty, dll');
                $table->decimal('amount', 10, 2);
                $table->text('description')->nullable();
                $table->timestamps();

                $table->foreign('salary_id')
                    ->references('id')
                    ->on('salaries')
                    ->onDelete('cascade');
                
                $table->index('salary_id');
            });
        }

        // Migrate existing JSON data to normalized tables
        $this->migrateSalaryData();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_deductions');
        Schema::dropIfExists('salary_allowances');
    }

    /**
     * Migrate existing JSON allowances and deductions to normalized tables
     */
    private function migrateSalaryData()
    {
        $salaries = DB::table('salaries')
            ->whereNotNull('allowances')
            ->orWhereNotNull('deductions')
            ->get();

        foreach ($salaries as $salary) {
            // Migrate allowances
            if (!empty($salary->allowances)) {
                $allowances = json_decode($salary->allowances, true);
                if (is_array($allowances)) {
                    foreach ($allowances as $type => $amount) {
                        DB::table('salary_allowances')->insert([
                            'salary_id' => $salary->id,
                            'type' => $type,
                            'amount' => $amount,
                            'created_at' => $salary->created_at ?? now(),
                            'updated_at' => $salary->updated_at ?? now(),
                        ]);
                    }
                }
            }

            // Migrate deductions
            if (!empty($salary->deductions)) {
                $deductions = json_decode($salary->deductions, true);
                if (is_array($deductions)) {
                    foreach ($deductions as $type => $amount) {
                        DB::table('salary_deductions')->insert([
                            'salary_id' => $salary->id,
                            'type' => $type,
                            'amount' => $amount,
                            'created_at' => $salary->created_at ?? now(),
                            'updated_at' => $salary->updated_at ?? now(),
                        ]);
                    }
                }
            }
        }
    }
}
