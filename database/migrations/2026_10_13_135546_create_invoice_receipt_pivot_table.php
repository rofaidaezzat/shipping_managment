<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convert invoices <-> receipts from one-to-many to many-to-many.
     *
     * Steps:
     *  1. Create the invoice_receipt pivot table.
     *  2. Migrate existing receipt.invoice_id rows into the pivot.
     *  3. Drop the FK and the invoice_id column from receipts.
     */
    public function up(): void
    {
        // ── 1. Create pivot table ────────────────────────────────────────
        Schema::create('invoice_receipt', function (Blueprint $table) {
            $table->id();

            $table->foreignId('receipt_id')
                  ->constrained('receipts')
                  ->onDelete('cascade');   // deleting a receipt removes its pivot rows

            $table->foreignId('invoice_id')
                  ->constrained('invoices')
                  ->onDelete('restrict');  // cannot delete an invoice that has receipts

            // How much of this receipt is allocated to this invoice
            $table->decimal('amount', 12, 2)->default(0);

            $table->timestamps();

            // A receipt can only appear once per invoice
            $table->unique(['receipt_id', 'invoice_id']);
        });

        // ── 2. Migrate existing data ─────────────────────────────────────
        DB::table('receipts')
            ->whereNotNull('invoice_id')
            ->get()
            ->each(function ($row) {
                DB::table('invoice_receipt')->insert([
                    'receipt_id' => $row->id,
                    'invoice_id' => $row->invoice_id,
                    'amount'     => $row->amount_paid,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        // ── 3. Drop invoice_id from receipts ─────────────────────────────
        Schema::table('receipts', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
        });
    }

    public function down(): void
    {
        // Restore invoice_id column on receipts (single invoice fallback)
        Schema::table('receipts', function (Blueprint $table) {
            $table->foreignId('invoice_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('invoices')
                  ->onDelete('restrict');
        });

        // Restore first linked invoice per receipt
        DB::table('invoice_receipt')
            ->get()
            ->each(function ($row) {
                DB::table('receipts')
                    ->where('id', $row->receipt_id)
                    ->whereNull('invoice_id')
                    ->update(['invoice_id' => $row->invoice_id]);
            });

        Schema::dropIfExists('invoice_receipt');
    }
};
