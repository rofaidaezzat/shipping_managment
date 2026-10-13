@extends('layout.app')

@section('title', 'Invoices - CargoPort')
@section('page_title', 'Invoices')
@section('page_description', 'Customer billing, invoices and payments management')

@section('content')
<div class="space-y-6">

    <!-- Action Bar & Stats Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-800">All Invoices</h3>
            <p class="text-sm text-slate-500">Record customer billing, payments and track balances</p>
        </div>
        <div class="flex items-center gap-3">
            @if(!empty($duplicateInvoiceIds))
                <button type="button" disabled
                        class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 border border-slate-300 text-slate-400 rounded-lg text-sm font-medium cursor-not-allowed select-none shadow-none opacity-80"
                        title="Creation disabled: You must resolve duplicate Invoice # before adding new invoices.">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    <span>Create Invoice</span>
                    <span class="px-1.5 py-0.5 text-[10px] font-bold bg-rose-100 text-rose-600 rounded uppercase tracking-wider">Disabled</span>
                </button>
            @else
                <a href="{{ route('invoices.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Invoice
                </a>
            @endif
        </div>
    </div>

    <!-- Duplicate Invoice IDs Danger Warning Alert -->
    @if(!empty($duplicateInvoiceIds))
        <div class="p-4 rounded-xl bg-rose-50 border-2 border-rose-300 text-rose-900 shadow-sm flex items-start gap-3.5">
            <div class="p-2 bg-rose-100 text-rose-600 rounded-lg shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h4 class="text-sm font-bold text-rose-800 uppercase tracking-wide">Danger: Duplicate Invoice # Detected</h4>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-200 text-rose-800">
                        {{ count($duplicateInvoiceIds) }} Duplicate {{ Str::plural('ID', count($duplicateInvoiceIds)) }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        🔒 Adding Invoices Locked
                    </span>
                </div>
                <p class="text-sm text-rose-700 mt-1 font-medium">
                    Not correct: two or more Invoice # have the same ID (<span class="font-mono font-bold text-rose-900">{{ implode(', ', $duplicateInvoiceIds) }}</span>). Invoices must have unique identifiers. <strong>The "Create Invoice" button has been disabled</strong> until these duplicate IDs are resolved.
                </p>
            </div>
        </div>
    @endif

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Invoices</span>
                <h4 class="text-2xl font-bold text-slate-900 mt-1">{{ $invoices->count() }}</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">All registered invoices</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Invoiced</span>
                <h4 class="text-2xl font-bold text-slate-900 mt-1">${{ number_format($totalInvoiced, 2) }}</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">المبلغ المطلوب دفعه</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Collected</span>
                <h4 class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($totalPaid, 2) }}</h4>
                <p class="text-[11px] text-emerald-700/80 mt-0.5">المبلغ المدفوع</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <div class="p-5 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Outstanding Due</span>
                <h4 class="text-2xl font-bold text-amber-600 mt-1">${{ number_format($totalOutstanding, 2) }}</h4>
                <p class="text-[11px] text-amber-700/80 mt-0.5">المتبقي غير المحصل</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Search Bar Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
        <form method="GET" action="{{ route('invoices.index') }}" class="flex items-center gap-3">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by Invoice ID or Customer Name..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-medium transition">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('invoices.index') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-sm">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Invoices Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-6">ID</th>
                    <th class="py-3.5 px-6">Invoice #</th>
                    <th class="py-3.5 px-6">Date</th>
                    <th class="py-3.5 px-6">Customer Name</th>
                    <th class="py-3.5 px-6 text-center">Status</th>
                    <th class="py-3.5 px-6 text-right">Amount Due</th>
                    <th class="py-3.5 px-6 text-right">Paid Amount</th>
                    <th class="py-3.5 px-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($invoices as $invoice)
                    @php
                        $isDuplicate = in_array($invoice->invoice_id, $duplicateInvoiceIds ?? []);
                    @endphp
                    <tr class="{{ $isDuplicate ? 'bg-rose-50/60 hover:bg-rose-100/50' : 'hover:bg-slate-50/60' }} transition">
                        <td class="py-4 px-6 font-mono text-slate-400 text-xs">#{{ $invoice->id }}</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('invoices.show', $invoice) }}"
                                   class="font-mono text-sm font-bold {{ $isDuplicate ? 'text-rose-600 hover:text-rose-800' : 'text-indigo-600 hover:text-indigo-800' }}">
                                    {{ $invoice->invoice_id }}
                                </a>
                                @if($isDuplicate)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200" title="Two or more invoices share this Invoice #">
                                        <svg class="w-3 h-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Duplicate
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-6 text-slate-600">
                            {{ $invoice->date ? $invoice->date->format('M d, Y') : '—' }}
                        </td>
                        <td class="py-4 px-6 font-medium text-slate-900">
                            {{ $invoice->customer_name }}
                        </td>
                        {{-- Status Badge --}}
                        <td class="py-4 px-6 text-center">
                            @if($invoice->is_paid)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Paid
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Unpaid
                                </span>
                            @endif
                        </td>
                        {{-- Amount Due (المفروض يدفع) --}}
                        <td class="py-4 px-6 text-right font-mono font-bold text-sm text-slate-900">
                            ${{ number_format($invoice->total_amount, 2) }}
                        </td>
                        {{-- Paid Amount (المدفوع) --}}
                        <td class="py-4 px-6 text-right font-mono font-semibold text-sm {{ $invoice->is_paid ? 'text-emerald-600' : 'text-slate-400' }}">
                            ${{ number_format($invoice->paid_amount, 2) }}
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-1">
                                {{-- View --}}
                                <a href="{{ route('invoices.show', $invoice) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 transition" title="View Invoice">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                {{-- Record Payment (only if unpaid) --}}
                                @if(!$invoice->is_paid)
                                    <a href="{{ route('invoices.receipts.create', $invoice) }}"
                                       class="p-1.5 text-slate-400 hover:text-emerald-600 transition" title="Record Payment">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="p-1.5 text-emerald-400 cursor-default" title="Already Paid">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </span>
                                @endif
                                {{-- Edit --}}
                                <a href="{{ route('invoices.edit', $invoice) }}" class="p-1.5 text-slate-400 hover:text-amber-600 transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                {{-- Delete: locked for paid invoices --}}
                                @if($invoice->is_paid)
                                    <span class="p-1.5 text-slate-200 cursor-not-allowed" title="Cannot delete: Invoice is Paid">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </span>
                                @else
                                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Are you sure you want to delete invoice {{ $invoice->invoice_id }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                            No invoices found. <a href="{{ route('invoices.create') }}" class="text-indigo-600 font-semibold hover:underline">Create the first invoice</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
