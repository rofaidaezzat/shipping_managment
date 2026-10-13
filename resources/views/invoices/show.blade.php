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
                <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                    ● Paid in Full
                </span>
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
                        <td class="py-4 text-right font-mono font-semibold text-slate-900">
                            ${{ number_format($invoice->paid_amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Invoice Totals Footer -->
        <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="text-xs text-slate-400">
                Created on {{ $invoice->created_at ? $invoice->created_at->format('M d, Y H:i') : 'N/A' }}
            </div>
            <div class="w-full sm:w-64 space-y-2 text-right">
                <div class="flex justify-between text-sm text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-mono">${{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-bold text-slate-900 pt-2 border-t border-slate-100">
                    <span>Total Paid:</span>
                    <span class="font-mono text-emerald-600">${{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
