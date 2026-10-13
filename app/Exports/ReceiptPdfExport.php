<?php

namespace App\Exports;

use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ReceiptPdfExport
{
    protected Receipt $receipt;

    public function __construct(Receipt $receipt)
    {
        $this->receipt = $receipt;
    }

    /**
     * Build and configure DomPDF instance.
     */
    public function generate()
    {
        $this->receipt->load('invoice');

        return Pdf::loadView('receipts.pdf', [
            'receipt' => $this->receipt,
        ])->setPaper('a4', 'portrait')
          ->setOption([
              'isHtml5ParserEnabled' => true,
              'isRemoteEnabled'      => true,
              'defaultFont'          => 'sans-serif',
          ]);
    }

    /**
     * Download the PDF file directly to client.
     */
    public function download(?string $filename = null): Response
    {
        $filename = $filename ?? sprintf('Receipt-REC-%04d.pdf', $this->receipt->id);
        return $this->generate()->download($filename);
    }

    /**
     * Stream PDF inline in the browser tab.
     */
    public function stream(?string $filename = null): Response
    {
        $filename = $filename ?? sprintf('Receipt-REC-%04d.pdf', $this->receipt->id);
        return $this->generate()->stream($filename);
    }
}
