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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->string('employee_code')->unique();
            $table->string('phone')->nullable();
            $table->string('designation');
            $table->date('joining_date');

            // Government Details
            $table->string('pan')->nullable();
            $table->string('uan')->nullable();
            $table->json('bank_details')->nullable();
            $table->string('pf_number')->nullable();

            // Salary Structure
            $table->decimal('basic_salary',12,2)->default(0);
            $table->decimal('hra',12,2)->default(0);
            $table->decimal('lta',12,2)->default(0);
            $table->decimal('special_allowance',12,2)->default(0);
            $table->decimal('conveyance_allowance',12,2)->default(0);
            $table->decimal('corporate_nps',12,2)->default(0);

            // Default Deductions
            $table->decimal('pf',12,2)->default(0);
            $table->decimal('esic',12,2)->default(0);
            $table->decimal('nps',12,2)->default(0);
            $table->decimal('labour_welfare_fund',12,2)->default(0);
            $table->decimal('professional_tax',12,2)->default(0);
            $table->decimal('income_tax',12,2)->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
