<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->unsigned();
            $table->date('month'); // Year-month for salary period
            $table->decimal('basic_salary', 10, 2);
            $table->json('allowances')->nullable(); // Store allowances as JSON
            $table->json('deductions')->nullable(); // Store deductions as JSON
            $table->decimal('net_salary', 10, 2);
            $table->timestamps();
            
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['employee_id', 'month']); // One salary per employee per month
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}

