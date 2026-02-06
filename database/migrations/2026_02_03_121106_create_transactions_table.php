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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('transaction_code')->unique();
            $table->string('patient_name');
            $table->string('insurance_id')->nullable();
            $table->foreignUuid('voucher_id')->nullable()->constrained('master_vouchers');
            $table->decimal('total_amount_original');
            $table->decimal('discount_amount')->default(0);
            $table->decimal('total_amount_final');
            $table->string('status');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
