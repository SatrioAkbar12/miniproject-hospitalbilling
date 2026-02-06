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
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->decimal('price',15, 2)->change();
            $table->decimal('discount_amount',15, 2)->change();
            $table->decimal('final_price',15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_details', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->change();
            $table->decimal('discount_amount', 8, 2)->change();
            $table->decimal('final_price', 8, 2)->change();
        });
    }
};
