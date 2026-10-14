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
            @foreach($receipt->invoices as $inv)
                <span class="text-slate-300">•</span>
                <a href="{{ route('invoices.show', $inv) }}"
                   class="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                    Invoice {{ $inv->invoice_id }}
                </a>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            {{-- DomPDF Download Button --}}
            <a href="{{ route('receipts.pdf', ['receipt' => $receipt, 'action' => 'download']) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition"
               title="Export and Download Official PDF Voucher">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Download PDF
            </a>

            {{-- Browser Print Button --}}
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>

            @if($receipt->status !== 'rejected')
                @php $invList = $receipt->invoices->pluck('invoice_id')->implode(', '); @endphp
                <form action="{{ route('receipts.destroy', $receipt) }}" method="POST" class="inline"
                      onsubmit="return confirmRejectReceipt(event, this, 'REC-{{ str_pad($receipt->id, 4, '0', STR_PAD_LEFT) }}', '{{ $invList }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition"
                            title="Reject Receipt & Revert Invoice to Unpaid">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Reject Receipt
                    </button>
                </form>
            @else
                <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Receipt Rejected
                </span>
            @endif
        </div>
    </div>

    @if($receipt->status === 'rejected')
        <div class="mb-6 bg-rose-50 border border-rose-200 rounded-xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-rose-800">This Receipt Has Been Rejected (Cancelled)</h4>
                <p class="text-xs text-rose-600 mt-0.5">This transaction was cancelled and the linked invoice balance has been reverted. This document is kept for audit purposes only.</p>
            </div>
        </div>
    @endif

    <!-- Official Printable Voucher Document -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 sm:p-10 relative overflow-hidden" id="receipt-voucher">
        {{-- Decorative Top Accent Bar --}}
        <div class="absolute top-0 left-0 right-0 h-2 {{ $receipt->status === 'rejected' ? 'bg-gradient-to-r from-rose-500 via-rose-600 to-rose-500' : 'bg-gradient-to-r from-emerald-500 via-indigo-600 to-emerald-500' }}"></div>

        <!-- Voucher Header -->
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 pb-8 border-b border-slate-200">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl {{ $receipt->status === 'rejected' ? 'bg-rose-600' : 'bg-indigo-600' }} text-white flex items-center justify-center shadow-lg shadow-indigo-500/20 shrink-0">
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
                @if($receipt->status === 'rejected')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300 mb-2 uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                        REJECTED / VOID
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 mb-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        OFFICIAL RECEIPT VOUCHER
                    </span>
                @endif
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
            <div class="bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-xl p-6 space-y-4">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Amount Received</span>
                        <h3 class="text-3xl sm:text-4xl font-mono font-extrabold text-emerald-700 mt-1">
                            ${{ number_format($receipt->amount_paid, 2) }}
                        </h3>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">Payment Method(s)</span>
                        <div class="mt-1 flex flex-wrap gap-1.5 sm:justify-end">
                            @forelse($receipt->paymentMethods as $pm)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-600 text-white shadow-sm">
                                    {{ $pm->method }}
                                </span>
                            @empty
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-semibold bg-emerald-600 text-white shadow-sm">
                                    {{ $receipt->payment_method ?? '—' }}
                                </span>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Payment breakdown table (when multiple methods used) --}}
                @if($receipt->paymentMethods->count() > 1)
                    <div class="border-t border-emerald-200 pt-4">
                        <p class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider mb-2">Payment Breakdown</p>
                        <div class="space-y-1.5">
                            @foreach($receipt->paymentMethods as $pm)
                                <div class="flex items-center justify-between bg-white/60 rounded-lg px-3 py-2 border border-emerald-100">
                                    <span class="text-sm font-medium text-emerald-900">{{ $pm->method }}</span>
                                    <span class="font-mono font-bold text-emerald-700">${{ number_format($pm->amount, 2) }}</span>
                                </div>
                            @endforeach
                            <div class="flex items-center justify-between bg-emerald-100 rounded-lg px-3 py-2 border border-emerald-200 mt-2">
                                <span class="text-sm font-bold text-emerald-900">Total</span>
                                <span class="font-mono font-bold text-emerald-800">${{ number_format($receipt->amount_paid, 2) }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Voucher Details Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                {{-- Payer Details (from first invoice) --}}
                <div class="p-5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Received From</span>
                    @php $firstInv = $receipt->invoices->first(); @endphp
                    <div>
                        <h4 class="text-base font-bold text-slate-900">{{ $firstInv?->customer_name ?? '—' }}</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Customer / Debtor</p>
                    </div>
                </div>

                {{-- Linked Invoices --}}
                <div class="p-5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">For Invoice Settlement</span>
                    @forelse($receipt->invoices as $inv)
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="{{ route('invoices.show', $inv) }}"
                                   class="font-mono font-bold text-indigo-700 text-sm hover:underline">
                                    {{ $inv->invoice_id }}
                                </a>
                                <p class="text-[11px] text-slate-400">{{ $inv->date?->format('M d, Y') }}</p>
                            </div>
                            <span class="font-mono font-semibold text-emerald-700 text-sm">
                                ${{ number_format($inv->pivot->amount, 2) }}
                            </span>
                        </div>
                        @if(!$loop->last)<div class="border-t border-slate-200"></div>@endif
                    @empty
                        <p class="text-sm text-slate-400 italic">No linked invoices.</p>
                    @endforelse
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
