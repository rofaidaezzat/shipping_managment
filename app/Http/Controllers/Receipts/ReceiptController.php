<?php

namespace App\Http\Controllers\Receipts;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Receipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    /**
     * Display a listing of all receipts across all invoices.
     */
    public function index(Request $request)
    {
        $query = Receipt::with('invoice');

        // Search by Receipt ID, Invoice ID, Customer Name, or Notes
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                // If numeric or REC- prefix
                $cleanedId = preg_replace('/[^0-9]/', '', $search);
                if (!empty($cleanedId)) {
                    $q->orWhere('id', (int) $cleanedId);
                }

                $q->orWhere('payment_method', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('invoice', function ($iq) use ($search) {
                      $iq->where('invoice_id', 'like', "%{$search}%")
                         ->orWhere('customer_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by payment method
        if ($method = $request->input('payment_method')) {
            $query->where('payment_method', $method);
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

        // Calculate statistics
        $totalReceipts = Receipt::count();
        $totalCollected = (float) Receipt::sum('amount_paid');
        $thisMonthCollected = (float) Receipt::whereYear('payment_date', now()->year)
                                             ->whereMonth('payment_date', now()->month)
                                             ->sum('amount_paid');
        $avgReceipt = $totalReceipts > 0 ? ($totalCollected / $totalReceipts) : 0;

        // Unpaid invoices available for recording payment
        $unpaidInvoices = Invoice::where('is_paid', false)->orderBy('date', 'desc')->get();

        return view('receipts.index', compact(
            'receipts',
            'totalReceipts',
            'totalCollected',
            'thisMonthCollected',
            'avgReceipt',
            'unpaidInvoices'
        ));
    }

    /**
     * Display the specified receipt voucher.
     */
    public function show(Receipt $receipt)
    {
        $receipt->load('invoice');
        return view('receipts.show', compact('receipt'));
    }

    /**
     * Show the form to record a new payment.
     * Can be invoked standalone or with an optional ?invoice_id=... query parameter.
     */
    public function create(Request $request)
    {
        $invoice = null;
        if ($invoiceId = $request->query('invoice_id')) {
            $invoice = Invoice::find($invoiceId);
            if ($invoice && $invoice->is_paid) {
                return redirect()->route('receipts.index')
                    ->with('error', "Invoice '{$invoice->invoice_id}' is already marked as Paid.");
            }
        }

        $unpaidInvoices = Invoice::where('is_paid', false)->orderBy('date', 'desc')->get();

        return view('receipts.create', compact('invoice', 'unpaidInvoices'));
    }

    /**
     * Show payment form for a specific invoice.
     */
    public function createForInvoice(Invoice $invoice)
    {
        if ($invoice->is_paid) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', "This invoice '{$invoice->invoice_id}' is already marked as Paid.");
        }

        $unpaidInvoices = Invoice::where('is_paid', false)->orderBy('date', 'desc')->get();

        return view('receipts.create', compact('invoice', 'unpaidInvoices'));
    }

    /**
     * Store a payment receipt (handles both standalone and modal submissions).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id'     => ['required', 'exists:invoices,id'],
            'amount_paid'    => ['required', 'numeric', 'min:0.01'],
            'payment_date'   => ['required', 'date'],
            'payment_method' => ['required', 'string', 'max:50'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        if ($invoice->is_paid) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 422,
                    'message' => "Invoice '{$invoice->invoice_id}' is already marked as Paid.",
                ], 422);
            }

            return redirect()->route('receipts.index')
                ->with('error', "Invoice '{$invoice->invoice_id}' is already marked as Paid.");
        }

        $receipt = DB::transaction(function () use ($invoice, $validated) {
            $rec = Receipt::create([
                'invoice_id'     => $invoice->id,
                'amount_paid'    => $validated['amount_paid'],
                'payment_date'   => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'notes'          => $validated['notes'] ?? null,
            ]);

            $invoice->update([
                'is_paid'     => true,
                'paid_amount' => $validated['amount_paid'],
                'paid_at'     => now(),
            ]);

            return $rec;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'code'    => 201,
                'message' => "Payment recorded for invoice {$invoice->invoice_id}.",
                'data'    => $receipt->load('invoice'),
            ], 201);
        }

        return redirect()->route('receipts.index')
            ->with('success', "Payment receipt #REC-" . str_pad($receipt->id, 4, '0', STR_PAD_LEFT) . " for \${$validated['amount_paid']} recorded successfully. Invoice {$invoice->invoice_id} is marked as Paid.");
    }

    /**
     * Store payment receipt for a specific invoice route (/invoices/{invoice}/receipts).
     */
    public function storeForInvoice(Request $request, Invoice $invoice)
    {
        if ($invoice->is_paid) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 422,
                    'message' => 'Invoice is already marked as Paid.',
                ], 422);
            }

            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'This invoice is already marked as Paid.');
        }

        $validated = $request->validate([
            'amount_paid'    => ['required', 'numeric', 'min:0.01'],
            'payment_date'   => ['required', 'date'],
            'payment_method' => ['required', 'string', 'max:50'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $receipt = DB::transaction(function () use ($invoice, $validated) {
            $rec = Receipt::create([
                'invoice_id'     => $invoice->id,
                'amount_paid'    => $validated['amount_paid'],
                'payment_date'   => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'notes'          => $validated['notes'] ?? null,
            ]);

            $invoice->update([
                'is_paid'     => true,
                'paid_amount' => $validated['amount_paid'],
                'paid_at'     => now(),
            ]);

            return $rec;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'code'    => 201,
                'message' => "Payment recorded for invoice {$invoice->invoice_id}.",
                'data'    => $invoice->fresh(),
            ], 201);
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', "Payment of \${$validated['amount_paid']} recorded for invoice {$invoice->invoice_id}. Invoice is now marked as Paid.");
    }

    /**
     * Redirect invoice-specific receipts list to global receipts page filtered by invoice.
     */
    public function invoiceReceipts(Invoice $invoice)
    {
        return redirect()->route('receipts.index', ['search' => $invoice->invoice_id]);
    }
}
