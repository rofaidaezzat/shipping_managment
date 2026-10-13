<?php

namespace App\Jobs;

use App\Exports\PortExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProcessDataJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('ProcessDataJob: Port export started in background.');

        // توليد ملف الإكسيل وتخزينه في storage/app/exports/ports.xlsx
        Excel::store(new PortExport, 'exports/ports.xlsx');

        Log::info('ProcessDataJob: Port export completed and saved to storage/app/exports/ports.xlsx.');
    }
}