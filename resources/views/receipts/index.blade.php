@extends('layout.app')

@section('title', 'Payment Receipts - CargoPort')
@section('page_title', 'Payment Receipts')
@section('page_description', 'Financial vouchers, payment records and collection audit trail')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">All Payment Receipts</h3>
            <p class="text-sm text-slate-500">Track incoming payments, customer vouchers and settlement history</p>
        </div>
        <div class="flex items-center gap-3">
            @if($unpaidInvoices->count() > 0)
                <a href="{{ route('receipts.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Record Payment
                    <span class="ml-1 px-1.5 py-0.2 bg-emerald-800/40 text-[11px] rounded-full">
                        {{ $unpaidInvoices->count() }} Unpaid
                    </span>
                </a>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    All Invoices Paid
                </span>
            @endif
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Receipts Count --}}
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Receipts</span>
                <h4 class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($totalReceipts) }}</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Vouchers generated</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8h4"/>
                </svg>
            </div>
        </div>

        {{-- Total Collected Amount --}}
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Collected</span>
                <h4 class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($totalCollected, 2) }}</h4>
                <p class="text-[11px] text-emerald-700/80 mt-0.5">All-time revenue settled</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- This Month's Collections --}}
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">This Month</span>
                <h4 class="text-2xl font-bold text-slate-900 mt-1">${{ number_format($thisMonthCollected, 2) }}</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">{{ now()->format('F Y') }}</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        {{-- Average Receipt --}}
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Average Receipt</span>
                <h4 class="text-2xl font-bold text-slate-900 mt-1">${{ number_format($avgReceipt, 2) }}</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Per settlement</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('receipts.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            {{-- Text Search --}}
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by Receipt #, Invoice ID, Customer, or Notes..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Payment Method Filter --}}
            <div class="w-full md:w-48">
                <select name="payment_method" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Payment Methods</option>
                    @foreach(['Cash', 'Bank Transfer', 'Credit Card', 'Cheque', 'Online Payment', 'Other'] as $method)
                        <option value="{{ $method }}" {{ request('payment_method') === $method ? 'selected' : '' }}>
                            {{ $method }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date Range (Optional) --}}
            <div class="flex items-center gap-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       title="Date From"
                       class="px-2.5 py-2 border border-slate-300 rounded-lg text-xs text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <span class="text-xs text-slate-400">to</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       title="Date To"
                       class="px-2.5 py-2 border border-slate-300 rounded-lg text-xs text-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-medium transition shadow-sm">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'payment_method', 'date_from', 'date_to']))
                    <a href="{{ route('receipts.index') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-sm transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Receipts Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Receipt #</th>
                        <th class="py-3.5 px-6">Invoice #</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Payment Date</th>
                        <th class="py-3.5 px-6">Method</th>
                        <th class="py-3.5 px-6 text-right">Amount Paid</th>
                        <th class="py-3.5 px-6">Notes</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($receipts as $receipt)
                        <tr class="hover:bg-slate-50/70 transition group">
                            {{-- Receipt # --}}
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-mono font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8h4"/>
                                    </svg>
                                    REC-{{ str_pad($receipt->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            {{-- Linked Invoice # --}}
                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($receipt->invoice)
                                    <a href="{{ route('invoices.show', $receipt->invoice) }}"
                                       class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-900 font-mono font-semibold transition group-hover:underline">
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        {{ $receipt->invoice->invoice_id }}
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs italic">Invoice #{{ $receipt->invoice_id }}</span>
                                @endif
                            </td>

                            {{-- Customer --}}
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                        {{ substr($receipt->invoice->customer_name ?? 'C', 0, 1) }}
                                    </div>
                                    <span class="font-medium text-slate-900">
                                        {{ $receipt->invoice->customer_name ?? '—' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Payment Date --}}
                            <td class="py-4 px-6 whitespace-nowrap text-slate-600 text-xs">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium text-slate-800">{{ $receipt->payment_date ? $receipt->payment_date->format('M d, Y') : '—' }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400 block mt-0.5">{{ $receipt->created_at ? $receipt->created_at->diffForHumans() : '' }}</span>
                            </td>

                            {{-- Payment Method --}}
                            <td class="py-4 px-6 whitespace-nowrap">
                                @php
                                    $methodColors = [
                                        'Cash'            => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'Bank Transfer'   => 'bg-sky-50 text-sky-700 border-sky-200',
                                        'Credit Card'     => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'Cheque'          => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Online Payment'  => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    ];
                                    $badgeClass = $methodColors[$receipt->payment_method] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeClass }}">
                                    {{ $receipt->payment_method }}
                                </span>
                            </td>

                            {{-- Amount Paid --}}
                            <td class="py-4 px-6 whitespace-nowrap text-right">
                                <span class="font-mono font-bold text-base text-emerald-600">
                                    ${{ number_format($receipt->amount_paid, 2) }}
                                </span>
                            </td>

                            {{-- Notes --}}
                            <td class="py-4 px-6 text-slate-500 text-xs max-w-xs truncate" title="{{ $receipt->notes }}">
                                {{ $receipt->notes ?? '—' }}
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-6 whitespace-nowrap text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    {{-- Print / View Voucher --}}
                                    <a href="{{ route('receipts.show', $receipt) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-700 border border-slate-200 hover:border-indigo-200 transition"
                                       title="View & Print Official Receipt Voucher">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        <span>Voucher</span>
                                    </a>

                                    @if($receipt->invoice)
                                        {{-- View Invoice --}}
                                        <a href="{{ route('invoices.show', $receipt->invoice) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                                           title="Go to Invoice">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Invoice</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8h4"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-semibold text-slate-800">No receipts found</h4>
                                    <p class="text-xs text-slate-500">
                                        @if(request()->hasAny(['search', 'payment_method', 'date_from', 'date_to']))
                                            No receipts matched your filter criteria. Try clearing filters.
                                        @else
                                            No payments have been recorded yet. Click "Record Payment" to settle an unpaid invoice.
                                        @endif
                                    </p>
                                    @if(request()->hasAny(['search', 'payment_method', 'date_from', 'date_to']))
                                        <a href="{{ route('receipts.index') }}" class="inline-block text-xs font-semibold text-indigo-600 hover:underline">
                                            Clear all filters
                                        </a>
                                    @elseif($unpaidInvoices->count() > 0)
                                        <a href="{{ route('receipts.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Record First Payment
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($receipts->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
