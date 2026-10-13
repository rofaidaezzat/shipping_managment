@extends('layout.app')

@section('title', 'Edit Invoice - CargoPort')
@section('page_title', 'Edit Invoice')
@section('page_description', 'Update invoice details and paid amounts')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Invoices
        </a>
        <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium">
            View Details →
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Edit Invoice #{{ $invoice->invoice_id }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Editing invoice ID #{{ $invoice->id }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-mono font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                ${{ number_format($invoice->paid_amount, 2) }}
            </span>
        </div>

        <form action="{{ route('invoices.update', $invoice) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Invoice ID / Number --}}
                <div>
                    <label for="invoice_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Invoice # <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="invoice_id" id="invoice_id"
                           value="{{ old('invoice_id', $invoice->invoice_id) }}"
                           maxlength="50" required
                           class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('invoice_id') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-mono uppercase">
                    @error('invoice_id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Invoice Date --}}
                <div>
                    <label for="date" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Invoice Date <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="date" id="date"
                           value="{{ old('date', $invoice->date ? $invoice->date->format('Y-m-d') : '') }}" required
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
                       value="{{ old('customer_name', $invoice->customer_name) }}"
                       maxlength="150" required
                       class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('customer_name') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                @error('customer_name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Invoice Amount (المبلغ المطلوب دفعه) --}}
            <div>
                <label for="total_amount" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Invoice Amount (Amount Due) <span class="text-rose-500">*</span>
                    <span class="text-slate-400 font-normal lowercase">(المبلغ المطلوب دفعه)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-semibold">$</span>
                    <input type="number" step="0.01" min="0" name="total_amount" id="total_amount"
                           value="{{ old('total_amount', $invoice->total_amount) }}" required
                           class="w-full pl-8 pr-4 py-2.5 rounded-lg border {{ $errors->has('total_amount') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-mono text-slate-900">
                </div>
                @error('total_amount')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Paid Amount Info Card --}}
            <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between text-xs">
                <span class="text-slate-500">Currently Paid Amount:</span>
                <span class="font-mono font-bold text-emerald-600">${{ number_format($invoice->paid_amount, 2) }}</span>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <button type="submit" form="delete-invoice-form"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Invoice
                </button>

                <div class="flex items-center gap-3">
                    <a href="{{ route('invoices.index') }}"
                       class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                        Update Invoice
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-invoice-form" action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="hidden"
              onsubmit="return confirm('Permanently delete invoice {{ $invoice->invoice_id }}?');">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
