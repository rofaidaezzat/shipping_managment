@extends('layout.app')

@section('title', 'Create Invoice - CargoPort')
@section('page_title', 'Create Invoice')
@section('page_description', 'Generate and register a new customer invoice')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Invoices
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <!-- Duplicate Warning Alert Banner if duplicates exist -->
        @if(!empty($duplicateInvoiceIds))
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border-2 border-rose-300 text-rose-900 shadow-sm flex items-start gap-3.5">
                <div class="p-2 bg-rose-100 text-rose-600 rounded-lg shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-bold text-rose-800 uppercase tracking-wide">Cannot Create Invoice (Locked)</h4>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-200 text-rose-800">
                            {{ count($duplicateInvoiceIds) }} Duplicate {{ Str::plural('ID', count($duplicateInvoiceIds)) }}
                        </span>
                    </div>
                    <p class="text-sm text-rose-700 mt-1 font-medium">
                        Duplicate Invoice # detected in system (<span class="font-mono font-bold text-rose-900">{{ implode(', ', $duplicateInvoiceIds) }}</span>). Adding new invoices is disabled until these duplicates are resolved in the invoice list.
                    </p>
                </div>
            </div>
        @endif

        <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Invoice Details</h3>
                <p class="text-xs text-slate-500 mt-0.5">Fill in invoice number, customer name and payment details</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        <form action="{{ route('invoices.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Invoice ID / Number --}}
                <div>
                    <label for="invoice_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Invoice # <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="invoice_id" id="invoice_id"
                           value="{{ $nextPreviewId }}" readonly
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 bg-slate-50 text-slate-700 text-sm font-mono uppercase cursor-not-allowed select-none">
                    <p class="text-[11px] text-slate-400 mt-1">Invoice reference.</p>
                </div>

                {{-- Invoice Date --}}
                <div>
                    <label for="date" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Invoice Date <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="date" id="date"
                           value="{{ old('date', date('Y-m-d')) }}" required
                           class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('date') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                    @error('date')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Customer Name --}}
            <div>
                <label for="customer_name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Customer Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="customer_name" id="customer_name"
                       value="{{ old('customer_name') }}"
                       placeholder="e.g. Acme Shipping Corp, John Doe" maxlength="150" required
                       class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('customer_name') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                @error('customer_name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Total Amount (المبلغ المطلوب دفعه) --}}
            <div>
                <label for="total_amount" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Invoice Amount (Amount Due) <span class="text-rose-500">*</span>
                    <span class="text-slate-400 font-normal lowercase">(المبلغ المطلوب دفعه)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-semibold">$</span>
                    <input type="number" step="0.01" min="0.01" name="total_amount" id="total_amount"
                           value="{{ old('total_amount') }}" required
                           placeholder="0.00"
                           class="w-full pl-8 pr-4 py-2.5 rounded-lg border {{ $errors->has('total_amount') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-mono text-slate-900">
                </div>
                @error('total_amount')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Payment Status Info --}}
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-amber-700">
                    <strong>Note:</strong> The invoice will be created as <strong>Unpaid ($0.00 Paid)</strong> with the specified amount due. When you record a payment, it will be marked as <strong>Paid</strong>.
                </p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('invoices.index') }}"
                   class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition">
                    Cancel
                </a>
                @if(!empty($duplicateInvoiceIds))
                    <button type="button" disabled
                            class="inline-flex items-center gap-2 px-5 py-2 bg-slate-100 border border-slate-300 text-slate-400 rounded-lg text-sm font-medium cursor-not-allowed select-none shadow-none opacity-80"
                            title="Cannot save: You must resolve duplicate Invoice # before adding new invoices.">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Save Invoice
                        <span class="px-1.5 py-0.5 text-[10px] font-bold bg-rose-100 text-rose-600 rounded uppercase tracking-wider">Disabled</span>
                    </button>
                @else
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                        Save Invoice
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
