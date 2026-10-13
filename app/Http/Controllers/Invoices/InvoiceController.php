<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::query();

        // Search by invoice_id or customer_name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $invoices = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->get();
        $totalInvoiced = $invoices->sum('total_amount');
        $totalPaid = $invoices->where('is_paid', true)->sum('paid_amount');
        $totalOutstanding = $invoices->where('is_paid', false)->sum('total_amount');

        // Identify duplicate invoice IDs
        $duplicateInvoiceIds = Invoice::select('invoice_id')
            ->groupBy('invoice_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('invoice_id')
            ->toArray();

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status'            => 'success',
                'code'              => 200,
                'message'           => 'Invoices retrieved successfully.',
                'results'           => $invoices->count(),
                'total_invoiced'    => $totalInvoiced,
                'total_paid'        => $totalPaid,
                'total_outstanding' => $totalOutstanding,
                'duplicates'        => $duplicateInvoiceIds,
                'data'              => $invoices,
            ], 200);
        }

        return view('invoices.index', compact('invoices', 'totalInvoiced', 'totalPaid', 'totalOutstanding', 'duplicateInvoiceIds'));
    }

    /**
     * Show form to create a new invoice.
     */
    public function create()
    {
        $year = date('Y');
        $counterType = "INV-{$year}";

        // Identify duplicate invoice IDs
        $duplicateInvoiceIds = Invoice::select('invoice_id')
            ->groupBy('invoice_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('invoice_id')
            ->toArray();

        // Get estimated next sequence number for preview
        $lastNumber = \Illuminate\Support\Facades\DB::table('invoice_counters')
            ->where('type', $counterType)
            ->value('last_number');

        if ($lastNumber === null) {
            $lastNumber = (int)(Invoice::max('id') ?? 0);
        }

        // Check highest existing number in invoices table to prevent any overlap
        $existingHighest = 0;
        $existingInvoices = Invoice::where('invoice_id', 'like', "{$counterType}-%")->pluck('invoice_id');
        foreach ($existingInvoices as $invId) {
            if (preg_match('/-(\d+)$/', $invId, $m)) {
                $existingHighest = max($existingHighest, (int)$m[1]);
            }
        }
        $effectiveLast = max((int)$lastNumber, $existingHighest);

        $nextPreviewId = sprintf('%s-%04d', $counterType, $effectiveLast + 1);

        return view('invoices.create', compact('nextPreviewId', 'duplicateInvoiceIds'));
    }

    /**
     * Store a newly created invoice with atomic lockForUpdate transaction.
     */
    public function store(Request $request)
    {
        // Block creation if duplicate invoice numbers already exist in system
        $duplicateInvoiceIds = Invoice::select('invoice_id')
            ->groupBy('invoice_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('invoice_id')
            ->toArray();

        if (!empty($duplicateInvoiceIds)) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 422,
                    'message' => 'Cannot create invoice: Duplicate Invoice numbers detected (' . implode(', ', $duplicateInvoiceIds) . '). Please resolve the duplicates first.',
                ], 422);
            }

            return redirect()->route('invoices.index')
                ->with('error', 'Cannot create invoice: Duplicate Invoice # detected (' . implode(', ', $duplicateInvoiceIds) . '). Please resolve the duplicates before adding new invoices.');
        }

        $validated = $request->validate([
            'date'          => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:150'],
            'total_amount'  => ['required', 'numeric', 'min:0'],
        ]);

        // 🔒 Atomic Transaction with Row-Level Lock (InnoDB lockForUpdate)
        // If 3 users submit simultaneously, MySQL queues them so they receive consecutive IDs (0004, 0005, 0006)
        $invoice = \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            $year = date('Y', strtotime($validated['date']));
            $counterType = "INV-{$year}";

            // 1. Lock the counter row exclusively
            $counter = \Illuminate\Support\Facades\DB::table('invoice_counters')
                ->where('type', $counterType)
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                // Initialize counter if not yet present
                $currentMax = (int)(Invoice::max('id') ?? 0);
                \Illuminate\Support\Facades\DB::table('invoice_counters')->insert([
                    'type'        => $counterType,
                    'last_number' => $currentMax,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                $counter = \Illuminate\Support\Facades\DB::table('invoice_counters')
                    ->where('type', $counterType)
                    ->lockForUpdate()
                    ->first();
            }

            // Also check highest number in existing invoices to avoid overlap
            $existingHighest = 0;
            $existingInvoices = Invoice::where('invoice_id', 'like', "{$counterType}-%")->pluck('invoice_id');
            foreach ($existingInvoices as $invId) {
                if (preg_match('/-(\d+)$/', $invId, $m)) {
                    $existingHighest = max($existingHighest, (int)$m[1]);
                }
            }

            $baseNumber = max((int)$counter->last_number, $existingHighest);

            // 2. Increment counter safely
            $nextNumber = $baseNumber + 1;

            \Illuminate\Support\Facades\DB::table('invoice_counters')
                ->where('id', $counter->id)
                ->update([
                    'last_number' => $nextNumber,
                    'updated_at'  => now(),
                ]);

            // 3. Format consecutive unique ID: e.g. INV-2026-0007
            $generatedInvoiceId = sprintf('%s-%04d', $counterType, $nextNumber);

            // 4. Create invoice record — total_amount (المطلوب دفعه), paid_amount defaults to 0, is_paid to false
            return Invoice::create([
                'invoice_id'    => $generatedInvoiceId,
                'date'          => $validated['date'],
                'customer_name' => $validated['customer_name'],
                'total_amount'  => $validated['total_amount'],
                'paid_amount'   => 0.00,
                'is_paid'       => false,
            ]);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'code'    => 201,
                'message' => 'Invoice created successfully.',
                'data'    => $invoice,
            ], 201);
        }

        return redirect()->route('invoices.index')
            ->with('success', "Invoice '{$invoice->invoice_id}' created successfully!");
    }

    /**
     * Display the specified invoice.
     */
    public function show(Request $request, Invoice $invoice)
    {
        $invoice->load('receipts');

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Invoice retrieved successfully.',
                'data'    => $invoice,
            ], 200);
        }

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show form to edit an existing invoice.
     */
    public function edit(Invoice $invoice)
    {
        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified invoice.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_id'    => ['required', 'string', 'max:50'],
            'date'          => ['required', 'date'],
            'customer_name' => ['required', 'string', 'max:150'],
            'total_amount'  => ['required', 'numeric', 'min:0'],
        ]);

        $invoice->update([
            'invoice_id'    => $validated['invoice_id'],
            'date'          => $validated['date'],
            'customer_name' => $validated['customer_name'],
            'total_amount'  => $validated['total_amount'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => 'Invoice updated successfully.',
                'data'    => $invoice,
            ], 200);
        }

        return redirect()->route('invoices.index')
            ->with('success', "Invoice '{$invoice->invoice_id}' updated successfully!");
    }

    /**
     * Delete an invoice.
     * Blocked if invoice is Paid (has_receipts) — cannot delete payment history.
     */
    public function destroy(Request $request, Invoice $invoice)
    {
        // Block deletion if invoice is marked as paid
        if ($invoice->is_paid) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 403,
                    'message' => "Cannot delete invoice '{$invoice->invoice_id}': it is marked as Paid. Paid invoices cannot be deleted.",
                ], 403);
            }

            return redirect()->route('invoices.index')
                ->with('error', "Cannot delete invoice '{$invoice->invoice_id}': it is already Paid. Paid invoices are protected from deletion.");
        }

        // Block deletion if invoice has payment receipts
        if ($invoice->hasReceipts()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 403,
                    'message' => "Cannot delete invoice '{$invoice->invoice_id}': it has payment receipts attached.",
                ], 403);
            }

            return redirect()->route('invoices.index')
                ->with('error', "Cannot delete invoice '{$invoice->invoice_id}': it has payment receipts and cannot be deleted.");
        }

        $invoiceId = $invoice->invoice_id;
        $invoice->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => "Invoice '{$invoiceId}' deleted successfully.",
            ], 200);
        }

        return redirect()->route('invoices.index')
            ->with('success', "Invoice '{$invoiceId}' deleted successfully!");
    }
}
