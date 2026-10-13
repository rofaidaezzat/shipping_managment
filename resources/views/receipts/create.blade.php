@extends('layout.app')

@section('title', ($invoice ? 'Record Payment - ' . $invoice->invoice_id : 'Record Payment') . ' - CargoPort')
@section('page_title', 'Record Payment')
@section('page_description', 'Enter payment details to settle an invoice and generate a receipt')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Back Link -->
    <div class="flex items-center gap-3">
        @if($invoice)
            <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Invoice {{ $invoice->invoice_id }}
            </a>
            <span class="text-slate-300">•</span>
        @endif
        <a href="{{ route('receipts.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            All Receipts
        </a>
    </div>

    @if($invoice)
        <!-- Invoice Summary Card -->
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-400/20 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-indigo-500 uppercase tracking-wider">Target Invoice</p>
                    <p class="text-lg font-bold font-mono text-indigo-900">{{ $invoice->invoice_id }}</p>
                    <p class="text-sm text-indigo-700">{{ $invoice->customer_name }}</p>
                </div>
            </div>
            <div class="text-right space-y-1">
                <div>
                    <span class="text-xs text-indigo-500 uppercase font-semibold tracking-wider">Amount Due (المطلوب)</span>
                    <p class="text-lg font-mono font-bold text-indigo-900">${{ number_format($invoice->total_amount, 2) }}</p>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Unpaid
                </span>
            </div>
        </div>
    @endif

    <!-- Payment Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Payment Details</h3>
                <p class="text-xs text-slate-500 mt-0.5">Fill in payment voucher details to mark the invoice as Paid</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <form action="{{ $invoice ? route('invoices.receipts.store', $invoice) : route('receipts.store') }}" method="POST" class="space-y-6">
            @csrf

            @if(!$invoice)
                {{-- Select Unpaid Invoice --}}
                <div>
                    <label for="invoice_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Select Unpaid Invoice <span class="text-rose-500">*</span>
                    </label>
                    @if(isset($unpaidInvoices) && $unpaidInvoices->count() > 0)
                        <select name="invoice_id" id="invoice_id" required
                                class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('invoice_id') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm bg-white font-mono">
                            <option value="">-- Choose an unpaid invoice --</option>
                            @foreach($unpaidInvoices as $inv)
                                <option value="{{ $inv->id }}" {{ old('invoice_id', request('invoice_id')) == $inv->id ? 'selected' : '' }}>
                                    {{ $inv->invoice_id }} — {{ $inv->customer_name }} (${{ number_format($inv->total_amount, 2) }})
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
                            No unpaid invoices found in the system. All invoices are currently paid.
                        </div>
                    @endif
                    @error('invoice_id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @else
                <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Amount Paid --}}
                <div>
                    <label for="amount_paid" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Amount Paid ($ USD) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-semibold">$</span>
                        <input type="number" step="0.01" min="0.01" name="amount_paid" id="amount_paid"
                               value="{{ old('amount_paid', $invoice ? $invoice->total_amount : '') }}" required autofocus
                               placeholder="0.00"
                               class="w-full pl-8 pr-4 py-2.5 rounded-lg border {{ $errors->has('amount_paid') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm font-mono text-slate-900">
                    </div>
                    @error('amount_paid')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Payment Date --}}
                <div>
                    <label for="payment_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Payment Date <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="payment_date" id="payment_date"
                           value="{{ old('payment_date', date('Y-m-d')) }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('payment_date') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
                    @error('payment_date')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Payment Method --}}
            <div>
                <label for="payment_method" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Payment Method <span class="text-rose-500">*</span>
                </label>
                <select name="payment_method" id="payment_method" required
                        class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('payment_method') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
                    @foreach(['Cash', 'Bank Transfer', 'Cheque', 'Credit Card', 'Online Payment', 'Other'] as $method)
                        <option value="{{ $method }}" {{ old('payment_method') === $method ? 'selected' : '' }}>
                            {{ $method }}
                        </option>
                    @endforeach
                </select>
                @error('payment_method')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Notes --}}
            <div>
                <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Notes <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <textarea name="notes" id="notes" rows="3" maxlength="500"
                          placeholder="e.g. Paid via wire transfer, reference #WIRE-2026-001"
                          class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('notes') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm resize-none">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Warning Notice -->
            <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-sm text-amber-700">
                    <strong>Note:</strong> Once you record this payment, the invoice will be permanently marked as <strong>Paid</strong> and <strong>cannot be deleted</strong> under strict database protection.
                </p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ $invoice ? route('invoices.show', $invoice) : route('receipts.index') }}"
                   class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm shadow-emerald-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Confirm Payment
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
