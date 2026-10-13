<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')
                  ->constrained('invoices')
                  ->onDelete('restrict'); // Cannot delete invoice if it has receipts
            $table->decimal('amount_paid', 12, 2);
            $table->date('payment_date');
            $table->string('payment_method', 50)->default('Cash'); // Cash, Bank Transfer, Cheque, etc.
            $table->string('notes', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
