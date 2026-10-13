<?php

namespace App\Exports;

use App\Models\Receipt;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReceiptExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch all receipts eager-loaded with invoice.
     *
     * @return Collection
     */
    public function collection(): Collection
    {
        return Receipt::with('invoice')
            ->orderBy('payment_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Column headers for Excel spreadsheet.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Receipt ID',
            'Invoice #',
            'Customer Name',
            'Amount Paid ($)',
            'Payment Date',
            'Payment Method',
            'Notes',
            'Created At',
        ];
    }

    /**
     * Map each receipt model to row values.
     *
     * @param Receipt $receipt
     * @return array
     */
    public function map($receipt): array
    {
        return [
            'REC-' . str_pad($receipt->id, 4, '0', STR_PAD_LEFT),
            $receipt->invoice->invoice_id ?? 'N/A',
            $receipt->invoice->customer_name ?? 'N/A',
            number_format($receipt->amount_paid, 2),
            $receipt->payment_date ? $receipt->payment_date->format('Y-m-d') : 'N/A',
            $receipt->payment_method,
            $receipt->notes ?? '',
            $receipt->created_at ? $receipt->created_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }
}
