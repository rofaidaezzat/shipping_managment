@extends('layout.app')

@section('title', 'Invoice ' . $invoice->invoice_id . ' - CargoPort')
@section('page_title', 'Invoice Details')
@section('page_description', 'Customer billing receipt and transaction overview')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Invoices
        </a>
        <div class="flex items-center gap-3">
            {{-- Record Payment button (only if unpaid) --}}
            @if(!$invoice->is_paid)
                <a href="{{ route('invoices.receipts.create', $invoice) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition shadow-sm shadow-emerald-400/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Record Payment
                </a>
            @else
                <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-sm font-semibold cursor-default">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Paid in Full
                </span>
            @endif
            <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center gap-1.5 px-4 py-2 border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-medium transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Invoice
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print / PDF
            </button>
        </div>
    </div>

    <!-- Invoice Printable Paper Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 sm:p-12">
        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-8 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">CargoPort</h2>
                    <p class="text-xs text-slate-400">Shipping & Logistics Invoicing</p>
                </div>
            </div>

            <div class="sm:text-right">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Invoice Number</span>
                <h3 class="text-2xl font-mono font-bold text-slate-900 mt-0.5">{{ $invoice->invoice_id }}</h3>
                @if($invoice->is_paid)
                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        ● Paid in Full
                        @if($invoice->paid_at)
                            — {{ $invoice->paid_at->format('M d, Y') }}
                        @endif
                    </span>
                @else
                    <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        ● Unpaid
                    </span>
                @endif
            </div>
        </div>

        <!-- Meta Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 py-8 border-b border-slate-100">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block mb-1">Billed To</span>
                <h4 class="text-lg font-bold text-slate-900">{{ $invoice->customer_name }}</h4>
                <p class="text-xs text-slate-500 mt-1">Verified Shipping Client</p>
            </div>
            <div class="sm:text-right space-y-2">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Issue Date</span>
                    <span class="text-sm font-medium text-slate-800">{{ $invoice->date ? $invoice->date->format('F d, Y') : '—' }}</span>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">System Record ID</span>
                    <span class="text-xs font-mono text-slate-500">#{{ $invoice->id }}</span>
                </div>
            </div>
        </div>

        <!-- Invoice Line Items Table -->
        <div class="py-8">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="py-3">Description</th>
                        <th class="py-3 text-right">Quantity</th>
                        <th class="py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <tr>
                        <td class="py-4">
                            <span class="font-medium text-slate-900 block">Maritime Shipping & Terminal Services</span>
                            <span class="text-xs text-slate-400">Container terminal handling and port logistics charges</span>
                        </td>
                        <td class="py-4 text-right font-mono text-slate-600">1</td>
                        <td class="py-4 text-right font-mono font-bold text-slate-900">
                            ${{ number_format($invoice->total_amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Invoice Totals Footer -->
        <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="text-xs text-slate-400">
                Created on {{ $invoice->created_at ? $invoice->created_at->format('M d, Y H:i') : 'N/A' }}
                @if($invoice->is_paid && $invoice->paid_at)
                    <br>Paid on {{ $invoice->paid_at->format('M d, Y H:i') }}
                @endif
            </div>
            <div class="w-full sm:w-72 space-y-2 text-right">
                <div class="flex justify-between text-sm text-slate-600">
                    <span>Invoice Amount (المطلوب):</span>
                    <span class="font-mono font-bold text-slate-900">
                        ${{ number_format($invoice->total_amount, 2) }}
                    </span>
                </div>
                <div class="flex justify-between text-sm text-slate-600">
                    <span>Paid Amount (المدفوع):</span>
                    <span class="font-mono font-bold {{ $invoice->is_paid ? 'text-emerald-600' : 'text-slate-400' }}">
                        ${{ number_format($invoice->paid_amount, 2) }}
                    </span>
                </div>
                <div class="flex justify-between text-base font-bold text-slate-900 pt-2 border-t border-slate-100">
                    <span>Status / Balance:</span>
                    @if($invoice->is_paid)
                        <span class="font-mono text-emerald-600">Settled (Paid)</span>
                    @else
                        <span class="font-mono text-amber-600">${{ number_format($invoice->remaining_amount, 2) }} Due</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Receipts Section --}}
    @if($invoice->receipts->isNotEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h4 class="text-sm font-bold text-slate-800">Payment Receipts</h4>
                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold">
                    {{ $invoice->receipts->count() }} {{ Str::plural('Payment', $invoice->receipts->count()) }}
                </span>
            </div>
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 border-b border-slate-100 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-6">Receipt #</th>
                        <th class="py-3 px-6">Date</th>
                        <th class="py-3 px-6">Method</th>
                        <th class="py-3 px-6">Notes</th>
                        <th class="py-3 px-6 text-right">Amount Paid</th>
                        <th class="py-3 px-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoice->receipts as $receipt)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-3.5 px-6 font-mono text-xs font-semibold text-indigo-600">
                                <a href="{{ route('receipts.show', $receipt) }}" class="hover:underline">
                                    REC-{{ str_pad($receipt->id, 4, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td class="py-3.5 px-6 text-slate-700">
                                {{ $receipt->payment_date ? $receipt->payment_date->format('M d, Y') : '—' }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    {{ $receipt->payment_method }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-slate-500 text-xs">{{ $receipt->notes ?? '—' }}</td>
                            <td class="py-3.5 px-6 text-right font-mono font-bold text-emerald-600">
                                ${{ number_format($receipt->amount_paid, 2) }}
                            </td>
                            <td class="py-3.5 px-6 text-center">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <a href="{{ route('receipts.show', $receipt) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-medium bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 border border-slate-200 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        Voucher
                                    </a>

                                    {{-- Download PDF --}}
                                    <a href="{{ route('receipts.pdf', ['receipt' => $receipt, 'action' => 'download']) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded text-xs font-medium bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition"
                                       title="Download Official PDF Voucher via DomPDF">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        PDF
                                    </a>

                                    {{-- Reject Receipt --}}
                                    <form action="{{ route('receipts.destroy', $receipt) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Reject receipt REC-{{ str_pad($receipt->id, 4, '0', STR_PAD_LEFT) }}? This invoice will return to UNPAID status.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 hover:border-rose-300 transition"
                                                title="Reject Receipt & Return to Unpaid">
                                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @elseif(!$invoice->is_paid)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-amber-800">No payments recorded yet.</p>
                <p class="text-xs text-amber-600 mt-0.5">This invoice is currently <strong>Unpaid</strong>. Click "Record Payment" above to mark it as paid.</p>
            </div>
            <a href="{{ route('invoices.receipts.create', $invoice) }}"
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition shrink-0">
                Record Payment
            </a>
        </div>
    @endif

</div>
@endsection
