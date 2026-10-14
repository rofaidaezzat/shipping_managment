<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create the payment_methods table.
     * Each row represents one payment method split for a receipt.
     * A receipt can have multiple payment_methods rows (Cash + Bank Transfer + Visa, etc.)
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();

            // Many-to-one: many payment method rows belong to one receipt
            $table->foreignId('receipt_id')
                  ->constrained('receipts')
                  ->onDelete('cascade'); // deleting receipt also removes its payment method rows

            $table->string('method', 50);        // e.g. Cash, Bank Transfer, Credit Card
            $table->decimal('amount', 12, 2);     // amount paid via this method
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
