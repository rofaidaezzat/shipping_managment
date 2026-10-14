<?php

namespace App\Jobs;

use App\Exports\PortExport;
use App\Exports\ReceiptExport;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessDataJob implements ShouldQueue
{
    use Queueable;

    public string $type;
    public mixed $data;

    /**
     * Create a new job instance.
     *
     * @param string $type  'ports_excel' | 'receipt_pdf' | 'receipts_excel'
     * @param mixed  $data  Optional model instance or identifier
     */
    public function __construct(string $type = 'ports_excel', mixed $data = null)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("ProcessDataJob: Background processing started for [{$this->type}].");

        switch ($this->type) {
            case 'receipt_pdf':
                $this->handleReceiptPdf();
                break;

            case 'receipts_excel':
                $this->handleReceiptsExcel();
                break;

            case 'ports_excel':
            default:
                $this->handlePortsExcel();
                break;
        }

        Log::info("ProcessDataJob: Background processing finished successfully for [{$this->type}].");
    }

    /**
     * Generate and store Receipt PDF voucher using DomPDF.
     */
    protected function handleReceiptPdf(): void
    {
        $receipt = null;

        if ($this->data instanceof Receipt) {
            $receipt = $this->data;
            $receipt->load(['invoices', 'paymentMethods']);
        } elseif (is_numeric($this->data)) {
            $receipt = Receipt::with(['invoices', 'paymentMethods'])->find($this->data);
        }

        if (! $receipt) {
            Log::warning("ProcessDataJob: No valid receipt provided for PDF generation.");
            return;
        }

        $formattedId = 'REC-' . str_pad($receipt->id, 4, '0', STR_PAD_LEFT);
        Log::info("ProcessDataJob: Generating DomPDF voucher for receipt #{$formattedId}...");

        $pdf = Pdf::loadView('receipts.pdf', ['receipt' => $receipt])
            ->setPaper('a4', 'portrait')
            ->setOption([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);

        $filepath = "exports/receipts/Receipt-{$formattedId}.pdf";
        Storage::put($filepath, $pdf->output());

        Log::info("ProcessDataJob: Receipt PDF saved successfully to [storage/app/{$filepath}].");
    }

    /**
     * Generate and store Receipts Excel spreadsheet using Maatwebsite Excel.
     */
    protected function handleReceiptsExcel(): void
    {
        Log::info('ProcessDataJob: Receipts Excel export started in background.');

        Excel::store(new ReceiptExport, 'exports/receipts.xlsx');

        Log::info('ProcessDataJob: Receipts Excel export completed and saved to storage/app/exports/receipts.xlsx.');
    }

    /**
     * Generate and store Ports Excel spreadsheet using Maatwebsite Excel.
     */
    protected function handlePortsExcel(): void
    {
        Log::info('ProcessDataJob: Port export started in background.');

        Excel::store(new PortExport, 'exports/ports.xlsx');

        Log::info('ProcessDataJob: Port export completed and saved to storage/app/exports/ports.xlsx.');
    }
}