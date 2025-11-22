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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->date('invoice_date');
            $table->json('customer');
            $table->json('seller');
            $table->json('bank_details');
            $table->string('gst_type');
            $table->json('gst_rate');
            $table->json('items');
            $table->decimal('advancePayment', 12, 2)->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('terms');
            $table->string('declaration');
            $table->string('signatureName')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
