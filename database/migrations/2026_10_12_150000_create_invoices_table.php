<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id', 50);
            $table->date('date');
            $table->string('customer_name', 150);
            $table->decimal('total_amount', 12, 2)->default(0); // المبلغ المفروض يدفع (Amount Due / Total)
            $table->decimal('paid_amount', 12, 2)->default(0);  // المبلغ المدفوع بالفعل
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
