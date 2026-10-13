@extends('layout.app')

@section('title', 'Receipt Voucher #REC-' . str_pad($receipt->id, 4, '0', STR_PAD_LEFT) . ' - CargoPort')
@section('page_title', 'Receipt Voucher')
@section('page_description', 'Official payment confirmation and receipt voucher')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Actions & Navigation Bar (Hidden during print) -->
    <div class="flex items-center justify-between print:hidden">
        <div class="flex items-center gap-3">
            <a href="{{ route('receipts.index') }}"
               class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Receipts
            </a>
            @if($receipt->invoice)
                <span class="text-slate-300">•</span>
                <a href="{{ route('invoices.show', $receipt->invoice) }}"
                   class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                    View Invoice {{ $receipt->invoice->invoice_id }}
                </a>
            @endif
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Voucher
            </button>
        </div>
    </div>

    <!-- Official Printable Voucher Document -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 sm:p-10 relative overflow-hidden" id="receipt-voucher">
        {{-- Decorative Top Accent Bar --}}
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-emerald-500 via-indigo-600 to-emerald-500"></div>

        <!-- Voucher Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-8 border-b border-slate-200">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/20 shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">CargoPort</h2>
                    <p class="text-xs text-slate-500">Shipping & Logistics Management</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Commercial Port Operations & Freight Solutions</p>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    OFFICIAL RECEIPT VOUCHER
                </span>
                <p class="text-2xl font-mono font-bold text-slate-900">
                    #REC-{{ str_pad($receipt->id, 4, '0', STR_PAD_LEFT) }}
                </p>
                <p class="text-xs text-slate-500 mt-1">
                    Issued: {{ $receipt->payment_date ? $receipt->payment_date->format('F d, Y') : '—' }}
                </p>
            </div>
        </div>

        <!-- Voucher Body -->
        <div class="py-8 space-y-6">
            {{-- Big Amount Highlight Box --}}
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Amount Received</span>
                    <h3 class="text-3xl sm:text-4xl font-mono font-extrabold text-emerald-700 mt-1">
                        ${{ number_format($receipt->amount_paid, 2) }}
                    </h3>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">Payment Method</span>
                    <div class="mt-1">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-sm font-semibold bg-emerald-600 text-white shadow-sm">
                            {{ $receipt->payment_method }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Voucher Details Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                {{-- Payer Details --}}
                <div class="p-5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Received From</span>
                    <div>
                        <h4 class="text-base font-bold text-slate-900">{{ $receipt->invoice->customer_name ?? '—' }}</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Customer / Debtor</p>
                    </div>
                </div>

                {{-- Reference Invoice --}}
                <div class="p-5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">For Invoice Settlement</span>
                    @if($receipt->invoice)
                        <div>
                            <span class="font-mono font-bold text-indigo-700 text-base">{{ $receipt->invoice->invoice_id }}</span>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Invoice Date: {{ $receipt->invoice->date ? $receipt->invoice->date->format('M d, Y') : '—' }}
                            </p>
                        </div>
                    @else
                        <p class="text-sm text-slate-600 font-mono">Invoice #{{ $receipt->invoice_id }}</p>
                    @endif
                </div>
            </div>

            {{-- Notes & Remarks --}}
            <div class="p-5 rounded-xl bg-slate-50 border border-slate-200/80">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Notes & Remarks</span>
                <p class="text-sm text-slate-700 mt-1 {{ $receipt->notes ? '' : 'italic text-slate-400' }}">
                    {{ $receipt->notes ?: 'No additional notes provided for this transaction.' }}
                </p>
            </div>
        </div>

        <!-- Voucher Signatures & Authorization -->
        <div class="pt-8 border-t border-slate-200 grid grid-cols-2 gap-8">
            <div class="text-center pt-8">
                <div class="w-48 mx-auto border-b border-slate-400 pb-1">
                    <span class="text-xs font-serif italic text-slate-400 select-none">Authorized Signatory</span>
                </div>
                <p class="text-xs font-semibold text-slate-700 mt-2">Cashier / Receiver</p>
                <p class="text-[10px] text-slate-400">CargoPort Finance Dept.</p>
            </div>

            <div class="text-center pt-8">
                <div class="w-48 mx-auto border-b border-slate-400 pb-1">
                    <span class="text-xs font-serif italic text-slate-400 select-none">Official Seal</span>
                </div>
                <p class="text-xs font-semibold text-slate-700 mt-2">Paid & Settled</p>
                <p class="text-[10px] text-slate-400">Strict Deletion Protection Active</p>
            </div>
        </div>

        <!-- Footer Notice -->
        <div class="mt-8 pt-4 border-t border-slate-100 text-center">
            <p class="text-[11px] text-slate-400">
                This is a computer-generated voucher issued by CargoPort Shipping System. All records are immutable under strict database protection.
            </p>
        </div>
    </div>

</div>

<style>
@media print {
    body {
        background: white !important;
    }
    aside, header, .print\:hidden {
        display: none !important;
    }
    main {
        padding: 0 !important;
        margin: 0 !important;
    }
    #receipt-voucher {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
}
</style>
@endsection
