<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();

            // Employee
            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            // Salary Month (Example: 2026-07)
            $table->date('salary_month');

            // Attendance Details
            $table->unsignedTinyInteger('working_days')->default(0);
            $table->decimal('paid_days', 5, 2)->default(0);
            $table->decimal('leaves', 5, 2)->default(0);
            $table->decimal('lop', 5, 2)->default(0);

            // Earnings
            $table->decimal('basic', 12, 2)->default(0);
            $table->decimal('hra', 12, 2)->default(0);
            $table->decimal('lta', 12, 2)->default(0);
            $table->decimal('special_allowance', 12, 2)->default(0);
            $table->decimal('conveyance_allowance', 12, 2)->default(0);
            $table->decimal('corporate_nps', 12, 2)->default(0);

            // Deductions
            $table->decimal('pf', 12, 2)->default(0);
            $table->decimal('esic', 12, 2)->default(0);
            $table->decimal('nps', 12, 2)->default(0);
            $table->decimal('labour_welfare_fund', 12, 2)->default(0);
            $table->decimal('professional_tax', 12, 2)->default(0);
            $table->decimal('income_tax', 12, 2)->default(0);

            // Totals
            $table->decimal('gross_salary', 12, 2)->default(0);
            $table->decimal('total_deduction', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);

            $table->timestamps();

            // Prevent duplicate payslip for same employee and month
            $table->unique(['employee_id', 'salary_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};
