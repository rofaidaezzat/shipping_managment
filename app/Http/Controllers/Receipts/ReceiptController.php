<?php

namespace App\Http\Controllers\Receipts;

use App\Exports\ReceiptPdfExport;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class ReceiptController extends Controller
{
    /**
     * Display a listing of all receipts across all invoices.
     */
    public function index(Request $request)
    {
        $query = Receipt::with('invoices', 'paymentMethods');

        // Search by Receipt ID, Invoice ID, Customer Name, or Notes
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $cleanedId = preg_replace('/[^0-9]/', '', $search);
                if (!empty($cleanedId)) {
                    $q->orWhere('id', (int) $cleanedId);
                }
                $q->orWhere('payment_method', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('invoices', function ($iq) use ($search) {
                      $iq->where('invoice_id', 'like', "%{$search}%")
                         ->orWhere('customer_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by payment method
        if ($method = $request->input('payment_method')) {
            $query->whereHas('paymentMethods', fn($q) => $q->where('method', $method));
        }

        // Filter by status (completed, rejected)
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by date range
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('payment_date', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('payment_date', '<=', $dateTo);
        }

        $receipts = $query->orderBy('payment_date', 'desc')
                          ->orderBy('id', 'desc')
                          ->paginate(15)
                          ->withQueryString();

        // Statistics (collected totals exclude rejected receipts)
        $totalReceipts       = Receipt::count();
        $totalCollected      = (float) Receipt::where('status', '!=', Receipt::STATUS_REJECTED)->sum('amount_paid');
        $thisMonthCollected  = (float) Receipt::where('status', '!=', Receipt::STATUS_REJECTED)
                                              ->whereYear('payment_date', now()->year)
                                              ->whereMonth('payment_date', now()->month)
                                              ->sum('amount_paid');
        $activeReceiptsCount = Receipt::where('status', '!=', Receipt::STATUS_REJECTED)->count();
        $avgReceipt          = $activeReceiptsCount > 0 ? ($totalCollected / $activeReceiptsCount) : 0;

        $unpaidInvoices = Invoice::where('is_paid', false)->orderBy('date', 'desc')->get();

        return view('receipts.index', compact(
            'receipts', 'totalReceipts', 'totalCollected',
            'thisMonthCollected', 'avgReceipt', 'unpaidInvoices'
        ));
    }

    /**
     * Display the specified receipt voucher.
     */
    public function show(Receipt $receipt)
    {
        $receipt->load('invoices', 'paymentMethods');
        return view('receipts.show', compact('receipt'));
    }

    /**
     * Download or stream official receipt voucher PDF via DomPDF export.
     */
    public function pdf(Request $request, Receipt $receipt)
    {
        $export = new ReceiptPdfExport($receipt);
        return $request->query('action') === 'download'
            ? $export->download()
            : $export->stream();
    }

    /**
     * Show the form to record a new payment (standalone or pre-selected invoice).
     */
    public function create(Request $request)
    {
        $preselectedInvoice = null;
        if ($invoiceId = $request->query('invoice_id')) {
            $preselectedInvoice = Invoice::find($invoiceId);
            if ($preselectedInvoice && $preselectedInvoice->is_paid) {
                return redirect()->route('receipts.index')
                    ->with('error', "Invoice '{$preselectedInvoice->invoice_id}' is already marked as Paid.");
            }
        }

        $unpaidInvoices = Invoice::where('is_paid', false)->orderBy('date', 'desc')->get();

        return view('receipts.create', compact('preselectedInvoice', 'unpaidInvoices'));
    }

    /**
     * Show payment form pre-selected for a specific invoice.
     */
    public function createForInvoice(Invoice $invoice)
    {
        if ($invoice->is_paid) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', "Invoice '{$invoice->invoice_id}' is already marked as Paid.");
        }

        $preselectedInvoice = $invoice;
        $unpaidInvoices     = Invoice::where('is_paid', false)->orderBy('date', 'desc')->get();

        return view('receipts.create', compact('preselectedInvoice', 'unpaidInvoices'));
    }

    /**
     * Store a new receipt covering one or more invoices.
     * Form posts: invoice_items[i][id], invoice_items[i][amount], payment_splits[i][method/amount]
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_date'              => ['required', 'date'],
            'notes'                     => ['nullable', 'string', 'max:500'],
            'invoice_items'             => ['required', 'array', 'min:1'],
            'invoice_items.*.id'        => ['required', 'exists:invoices,id'],
            'invoice_items.*.amount'    => ['required', 'numeric', 'min:0.01'],
            'payment_splits'            => ['required', 'array', 'min:1'],
            'payment_splits.*.method'   => ['required', 'string', 'max:50'],
            'payment_splits.*.amount'   => ['required', 'numeric', 'min:0.01'],
        ]);

        [$splits, $totalPaid, $methodSummary] = $this->resolvePaymentData($request);
        $invoiceItems = $this->resolveInvoiceItems($request);

        // Validate no already-paid invoices
        $invoiceIds = collect($invoiceItems)->pluck('id');
        $alreadyPaid = Invoice::whereIn('id', $invoiceIds)->where('is_paid', true)->pluck('invoice_id');
        if ($alreadyPaid->isNotEmpty()) {
            return back()->withErrors(['invoice_items' => "These invoices are already paid: " . $alreadyPaid->implode(', ')])->withInput();
        }

        $createdReceipts = DB::transaction(function () use ($request, $splits, $invoiceItems) {
            $created = [];

            // Working pool of invoice amounts to be covered
            $invoicePool = collect($invoiceItems)->map(function ($item) {
                return [
                    'id'        => (int) $item['id'],
                    'remaining' => (float) $item['amount'],
                ];
            })->all();

            $poolIndex = 0;

            // Generate one separate Receipt row for each payment method
            foreach ($splits as $split) {
                $splitAmount = (float) $split['amount'];
                $splitMethod = $split['method'];

                $rec = Receipt::create([
                    'amount_paid'    => $splitAmount,
                    'payment_date'   => $request->input('payment_date'),
                    'payment_method' => $splitMethod,
                    'notes'          => $request->input('notes'),
                    'status'         => Receipt::STATUS_COMPLETED,
                ]);

                // Create the PaymentMethod record for this receipt
                PaymentMethod::create([
                    'receipt_id' => $rec->id,
                    'method'     => $splitMethod,
                    'amount'     => $splitAmount,
                ]);

                // Allocate this split's amount across the invoices in the pool
                $amountToAllocate = $splitAmount;
                while ($amountToAllocate > 0.0001 && $poolIndex < count($invoicePool)) {
                    $invId        = $invoicePool[$poolIndex]['id'];
                    $invRemaining = $invoicePool[$poolIndex]['remaining'];

                    if ($invRemaining <= 0.0001) {
                        $poolIndex++;
                        continue;
                    }

                    $allocated = min($amountToAllocate, $invRemaining);
                    $allocated = round($allocated, 2);

                    // Attach to invoice_receipt pivot
                    $rec->invoices()->attach($invId, ['amount' => $allocated]);

                    $amountToAllocate = round($amountToAllocate - $allocated, 2);
                    $invoicePool[$poolIndex]['remaining'] = round($invRemaining - $allocated, 2);

                    if ($invoicePool[$poolIndex]['remaining'] <= 0.0001) {
                        $poolIndex++;
                    }
                }

                // If all pools are filled but a tiny fraction remains, allocate to last invoice
                if ($amountToAllocate > 0.0001 && !empty($invoiceItems)) {
                    $lastInvId = end($invoiceItems)['id'];
                    if (!$rec->invoices()->where('invoice_id', $lastInvId)->exists()) {
                        $rec->invoices()->attach($lastInvId, ['amount' => $amountToAllocate]);
                    } else {
                        DB::table('invoice_receipt')
                            ->where('receipt_id', $rec->id)
                            ->where('invoice_id', $lastInvId)
                            ->increment('amount', $amountToAllocate);
                    }
                }

                $created[] = $rec;
            }

            // Recalculate total paid for each invoice across all active receipts
            foreach ($invoiceItems as $item) {
                $invoice = Invoice::findOrFail($item['id']);

                $totalForInvoice = (float) DB::table('invoice_receipt')
                    ->join('receipts', 'receipts.id', '=', 'invoice_receipt.receipt_id')
                    ->where('invoice_receipt.invoice_id', $invoice->id)
                    ->where('receipts.status', '!=', Receipt::STATUS_REJECTED)
                    ->sum('invoice_receipt.amount');

                $isFullyPaid = $totalForInvoice >= (float) $invoice->total_amount && (float) $invoice->total_amount > 0;

                $invoice->update([
                    'is_paid'     => $isFullyPaid,
                    'paid_amount' => $totalForInvoice,
                    'paid_at'     => $isFullyPaid ? now() : null,
                ]);
            }

            return $created;
        });

        $receiptCount = count($createdReceipts);
        $receiptCodes = collect($createdReceipts)
            ->map(fn($r) => '#REC-' . str_pad($r->id, 4, '0', STR_PAD_LEFT))
            ->implode(', ');

        $invoiceNums = collect($invoiceItems)
            ->map(fn($i) => Invoice::find($i['id'])->invoice_id ?? '')
            ->filter()
            ->implode(', ');

        if ($receiptCount > 1) {
            $msg = "{$receiptCount} receipts ({$receiptCodes}) totaling \$" . number_format($totalPaid, 2) . " created for invoice(s): {$invoiceNums}.";
        } else {
            $firstReceipt = $createdReceipts[0];
            $msg = "Receipt {$receiptCodes} (\$" . number_format($totalPaid, 2) . " via {$firstReceipt->payment_method}) recorded for invoice(s): {$invoiceNums}.";
        }

        Alert::success('Payment Recorded', $msg);

        // Redirect back to invoice if referrer came from that invoice
        $firstInvoiceId = $invoiceItems[0]['id'] ?? null;
        if ($firstInvoiceId && count($invoiceItems) === 1
            && $request->header('referer')
            && str_contains($request->header('referer'), "/invoices/{$firstInvoiceId}")) {
            return redirect()->route('invoices.show', $firstInvoiceId)->with('success', $msg);
        }

        return redirect()->route('receipts.index')->with('success', $msg);
    }

    /**
     * Store payment for a specific invoice (from invoice show page).
     * Still supports adding other invoices from the form.
     */
    public function storeForInvoice(Request $request, Invoice $invoice)
    {
        if ($invoice->is_paid) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', "Invoice '{$invoice->invoice_id}' is already marked as Paid.");
        }

        // Reuse the main store logic
        return $this->store($request);
    }

    /**
     * Redirect to global receipts page filtered by invoice.
     */
    public function invoiceReceipts(Invoice $invoice)
    {
        return redirect()->route('receipts.index', ['search' => $invoice->invoice_id]);
    }

    /**
     * Reject / Cancel a receipt → marks receipt as rejected (not deleted) and reverts linked invoices back to their correct paid status.
     */
    public function destroy(Request $request, Receipt $receipt)
    {
        if ($receipt->isRejected()) {
            $msg = "Receipt #REC-" . str_pad($receipt->id, 4, '0', STR_PAD_LEFT) . " is already rejected.";
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'code' => 422, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $receipt->load('invoices');
        $affectedInvoices = $receipt->invoices;
        $receiptId        = $receipt->id;
        $amountPaid       = $receipt->amount_paid;

        DB::transaction(function () use ($receipt, $affectedInvoices) {
            // Mark receipt as rejected — do NOT delete receipt, preserve payment history & audit trail
            $receipt->update([
                'status' => Receipt::STATUS_REJECTED,
            ]);

            // Re-evaluate each linked invoice excluding rejected receipts
            foreach ($affectedInvoices as $invoice) {
                $totalRemaining = (float) DB::table('invoice_receipt')
                    ->join('receipts', 'receipts.id', '=', 'invoice_receipt.receipt_id')
                    ->where('invoice_receipt.invoice_id', $invoice->id)
                    ->where('receipts.status', '!=', Receipt::STATUS_REJECTED)
                    ->sum('invoice_receipt.amount');

                $isFullyPaid = $totalRemaining > 0
                    && $totalRemaining >= (float) $invoice->total_amount
                    && (float) $invoice->total_amount > 0;

                $invoice->update([
                    'is_paid'     => $isFullyPaid,
                    'paid_amount' => $totalRemaining,
                    'paid_at'     => $isFullyPaid ? $invoice->paid_at : null,
                ]);
            }
        });

        $invoiceIds = $affectedInvoices->pluck('invoice_id')->implode(', ');
        $receiptNum = 'REC-' . str_pad($receiptId, 4, '0', STR_PAD_LEFT);
        $msg = "Receipt #{$receiptNum} (\$" . number_format($amountPaid, 2) . ") was rejected."
             . ($invoiceIds ? " Invoice(s) {$invoiceIds} updated." : '');

        Alert::success('Receipt Rejected', $msg);

        if ($request->wantsJson()) {
            return response()->json(['status' => 'success', 'code' => 200, 'message' => $msg], 200);
        }

        // Redirect back to invoice if there was exactly one invoice
        $firstInvoice = $affectedInvoices->first();
        if ($firstInvoice && $affectedInvoices->count() === 1
            && $request->header('referer')
            && str_contains($request->header('referer'), "/invoices/{$firstInvoice->id}")) {
            return redirect()->route('invoices.show', $firstInvoice)->with('success', $msg);
        }

        return redirect()->route('receipts.index')->with('success', $msg);
    }

    /**
     * Alias for destroy — POST /receipts/{receipt}/reject.
     */
    public function reject(Request $request, Receipt $receipt)
    {
        return $this->destroy($request, $receipt);
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    /**
     * Extract and normalise payment_splits from the request.
     * Returns [$splits, $totalPaid, $methodSummary].
     */
    private function resolvePaymentData(Request $request): array
    {
        $splits = collect($request->input('payment_splits', []))
            ->filter(fn($s) => !empty($s['method']) && isset($s['amount']) && (float)$s['amount'] > 0)
            ->map(fn($s) => ['method' => trim($s['method']), 'amount' => round((float)$s['amount'], 2)])
            ->values()
            ->toArray();

        $totalPaid     = collect($splits)->sum('amount');
        $methodSummary = collect($splits)->pluck('method')->unique()->implode(' + ');

        return [$splits, $totalPaid, $methodSummary];
    }

    /**
     * Extract and normalise invoice_items from the request.
     * Returns array of ['id' => int, 'amount' => float].
     */
    private function resolveInvoiceItems(Request $request): array
    {
        return collect($request->input('invoice_items', []))
            ->filter(fn($i) => !empty($i['id']) && isset($i['amount']) && (float)$i['amount'] > 0)
            ->map(fn($i) => ['id' => (int)$i['id'], 'amount' => round((float)$i['amount'], 2)])
            ->values()
            ->toArray();
    }
}
