@extends('layout.app')

@section('title', ($preselectedInvoice ? 'Record Payment - ' . $preselectedInvoice->invoice_id : 'Record Payment') . ' - CargoPort')
@section('page_title', 'Record Payment')
@section('page_description', 'Record invoice settlement — adding multiple payment methods generates separate receipt rows for the invoice')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Back Link -->
    <div class="flex items-center gap-3">
        @if($preselectedInvoice)
            <a href="{{ route('invoices.show', $preselectedInvoice) }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Invoice {{ $preselectedInvoice->invoice_id }}
            </a>
            <span class="text-slate-300">•</span>
        @endif
        <a href="{{ route('receipts.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            All Receipts
        </a>
    </div>

    @if($preselectedInvoice)
        <!-- Preselected Invoice Banner -->
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-md shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-indigo-500 uppercase tracking-wider">Pre-selected Invoice</p>
                    <p class="text-lg font-bold font-mono text-indigo-900">{{ $preselectedInvoice->invoice_id }}</p>
                    <p class="text-sm text-indigo-700">{{ $preselectedInvoice->customer_name }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs text-indigo-500 uppercase font-semibold tracking-wider">Amount Due</span>
                <p class="text-lg font-mono font-bold text-indigo-900">${{ number_format($preselectedInvoice->total_amount, 2) }}</p>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Unpaid
                </span>
            </div>
        </div>
    @endif

    <!-- Main Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 space-y-8">

        <div class="flex items-center justify-between pb-5 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Payment Details</h3>
                <p class="text-xs text-slate-500 mt-0.5">Each payment method entered will create its own receipt voucher record</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <form action="{{ $preselectedInvoice ? route('invoices.receipts.store', $preselectedInvoice) : route('receipts.store') }}"
              method="POST" class="space-y-8" id="paymentForm">
            @csrf

            {{-- Payment Date --}}
            <div>
                <label for="payment_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Payment Date <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="payment_date" id="payment_date"
                       value="{{ old('payment_date', date('Y-m-d')) }}" required
                       class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('payment_date') ? 'border-rose-400' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
                @error('payment_date')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- ══════════════ INVOICES SECTION ══════════════ --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-indigo-700">
                            Invoices Covered <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-slate-400 mt-0.5">Select one or more unpaid invoices and the amount to allocate</p>
                    </div>
                    <button type="button" id="addInvoiceBtn"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg border border-indigo-200 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Invoice
                    </button>
                </div>

                <div id="invoicesContainer" class="space-y-3">
                    {{-- First invoice row --}}
                    <div class="invoice-row flex items-center gap-3 bg-indigo-50/50 rounded-xl p-3 border border-indigo-100">
                        <div class="flex-1">
                            <label class="block text-[10px] font-semibold text-indigo-500 uppercase mb-1">Invoice</label>
                            <select name="invoice_items[0][id]" required
                                    class="invoice-select w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white font-mono">
                                <option value="">-- Select unpaid invoice --</option>
                                @foreach($unpaidInvoices as $inv)
                                    <option value="{{ $inv->id }}"
                                            data-amount="{{ $inv->total_amount }}"
                                            data-label="{{ $inv->invoice_id }} — {{ $inv->customer_name }}"
                                            {{ ($preselectedInvoice && $preselectedInvoice->id == $inv->id) ? 'selected' : '' }}>
                                        {{ $inv->invoice_id }} — {{ $inv->customer_name }} (Due: ${{ number_format($inv->total_amount, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-44">
                            <label class="block text-[10px] font-semibold text-indigo-500 uppercase mb-1">Amount ($)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                                <input type="number" name="invoice_items[0][amount]" step="0.01" min="0.01" required
                                       placeholder="0.00"
                                       value="{{ $preselectedInvoice ? $preselectedInvoice->total_amount : '' }}"
                                       class="inv-amount w-full pl-7 pr-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-mono">
                            </div>
                        </div>
                        <button type="button" class="remove-invoice mt-5 w-8 h-8 rounded-lg text-slate-300 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center transition opacity-0 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Invoice subtotal --}}
                <div class="mt-3 flex justify-end">
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg px-4 py-2 text-sm">
                        <span class="text-indigo-600 font-semibold">Invoice Total:</span>
                        <span id="invTotal" class="font-mono font-bold text-indigo-800 ml-1">$0.00</span>
                    </div>
                </div>

                @error('invoice_items')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- ══════════════ PAYMENT METHODS SECTION ══════════════ --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-emerald-700">
                            Payment Methods <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-slate-400 mt-0.5">Split across Cash, Bank Transfer, etc. (Each method will generate a separate receipt row for the invoice)</p>
                    </div>
                    <button type="button" id="addSplitBtn"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg border border-emerald-200 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Method
                    </button>
                </div>

                <div id="splitsContainer" class="space-y-3">
                    <div class="split-row flex items-center gap-3 bg-emerald-50/50 rounded-xl p-3 border border-emerald-100">
                        <div class="flex-1">
                            <label class="block text-[10px] font-semibold text-emerald-600 uppercase mb-1">Method</label>
                            <select name="payment_splits[0][method]" required class="method-select w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
                                @foreach(['Cash', 'Bank Transfer', 'Credit Card', 'Cheque', 'Online Payment', 'Other'] as $m)
                                    <option value="{{ $m }}" {{ old('payment_splits.0.method', 'Cash') === $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-44">
                            <label class="block text-[10px] font-semibold text-emerald-600 uppercase mb-1">Amount ($)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                                <input type="number" name="payment_splits[0][amount]" step="0.01" min="0.01" required
                                       placeholder="0.00"
                                       value="{{ $preselectedInvoice ? $preselectedInvoice->total_amount : '' }}"
                                       class="amount-input w-full pl-7 pr-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm font-mono">
                            </div>
                        </div>
                        <button type="button" class="remove-split mt-5 w-8 h-8 rounded-lg text-slate-300 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center transition opacity-0 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Payment subtotal --}}
                <div class="mt-3 flex justify-end">
                    <div class="bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-2 text-sm">
                        <span class="text-emerald-600 font-semibold">Payment Total:</span>
                        <span id="payTotal" class="font-mono font-bold text-emerald-800 ml-1">$0.00</span>
                    </div>
                </div>

                @error('payment_splits')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- ══════════════ BALANCE BAR ══════════════ --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-5 py-4 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6 text-sm">
                    <span class="text-slate-500">Invoice Total: <strong id="balInv" class="font-mono text-indigo-700">$0.00</strong></span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-500">Payment Total: <strong id="balPay" class="font-mono text-emerald-700">$0.00</strong></span>
                </div>
                <div id="balanceBadge" class="text-sm font-semibold px-3 py-1 rounded-full border transition">
                    Difference: <span id="balDiff" class="font-mono">$0.00</span>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Notes <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <textarea name="notes" id="notes" rows="2" maxlength="500"
                          placeholder="e.g. Partial cash + wire ref #WIRE-001"
                          class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm resize-none">{{ old('notes') }}</textarea>
            </div>

            <!-- Warning -->
            <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p class="text-sm text-amber-700">
                    <strong>Note:</strong> Once recorded, each fully-covered invoice will be marked <strong>Paid</strong> and is <strong>protected from deletion</strong>.
                </p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ $preselectedInvoice ? route('invoices.show', $preselectedInvoice) : route('receipts.index') }}"
                   class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition">
                    Cancel
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Confirm Payment
                </button>
            </div>
        </form>
    </div>

</div>

<script>
(function () {
    const METHODS = ['Cash', 'Bank Transfer', 'Credit Card', 'Cheque', 'Online Payment', 'Other'];

    const invoicesContainer = document.getElementById('invoicesContainer');
    const splitsContainer   = document.getElementById('splitsContainer');
    const addInvoiceBtn     = document.getElementById('addInvoiceBtn');
    const addSplitBtn       = document.getElementById('addSplitBtn');

    const invTotalEl  = document.getElementById('invTotal');
    const payTotalEl  = document.getElementById('payTotal');
    const balInvEl    = document.getElementById('balInv');
    const balPayEl    = document.getElementById('balPay');
    const balDiffEl   = document.getElementById('balDiff');
    const balBadge    = document.getElementById('balanceBadge');

    let invCount  = 1;
    let payCount  = 1;

    @php
        $invoiceOptionsData = $unpaidInvoices->map(function ($i) {
            return [
                'id'            => $i->id,
                'invoice_id'    => $i->invoice_id,
                'customer_name' => $i->customer_name,
                'total_amount'  => (float) $i->total_amount,
            ];
        })->values();
    @endphp
    // Unpaid invoice data from server
    const invoiceOptions = @json($invoiceOptionsData);

    // ── Build select options HTML ─────────────────────────────────────
    function buildInvoiceOptions(selectedId = '') {
        return invoiceOptions.map(inv =>
            `<option value="${inv.id}" data-amount="${inv.total_amount}" ${inv.id == selectedId ? 'selected' : ''}>
                ${inv.invoice_id} — ${inv.customer_name} (Due: $${Number(inv.total_amount).toFixed(2)})
            </option>`
        ).join('');
    }

    // ── Build invoice row ─────────────────────────────────────────────
    function buildInvoiceRow(index) {
        const div = document.createElement('div');
        div.className = 'invoice-row flex items-center gap-3 bg-indigo-50/50 rounded-xl p-3 border border-indigo-100';
        div.innerHTML = `
            <div class="flex-1">
                <label class="block text-[10px] font-semibold text-indigo-500 uppercase mb-1">Invoice</label>
                <select name="invoice_items[${index}][id]" required class="invoice-select w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white font-mono">
                    <option value="">-- Select unpaid invoice --</option>
                    ${buildInvoiceOptions()}
                </select>
            </div>
            <div class="w-44">
                <label class="block text-[10px] font-semibold text-indigo-500 uppercase mb-1">Amount ($)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                    <input type="number" name="invoice_items[${index}][amount]" step="0.01" min="0.01" required
                           placeholder="0.00" class="inv-amount w-full pl-7 pr-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-mono">
                </div>
            </div>
            <button type="button" class="remove-invoice mt-5 w-8 h-8 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>`;
        // Auto-fill amount when invoice selected
        div.querySelector('.invoice-select').addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const amt = parseFloat(opt.dataset.amount || 0);
            div.querySelector('.inv-amount').value = amt > 0 ? amt.toFixed(2) : '';
            recalc();
        });
        div.querySelector('.inv-amount').addEventListener('input', recalc);
        return div;
    }

    // ── Build payment split row ───────────────────────────────────────
    function buildSplitRow(index) {
        const div = document.createElement('div');
        div.className = 'split-row flex items-center gap-3 bg-emerald-50/50 rounded-xl p-3 border border-emerald-100';
        div.innerHTML = `
            <div class="flex-1">
                <label class="block text-[10px] font-semibold text-emerald-600 uppercase mb-1">Method</label>
                <select name="payment_splits[${index}][method]" required class="method-select w-full px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm bg-white">
                    ${METHODS.map(m => `<option value="${m}">${m}</option>`).join('')}
                </select>
            </div>
            <div class="w-44">
                <label class="block text-[10px] font-semibold text-emerald-600 uppercase mb-1">Amount ($)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">$</span>
                    <input type="number" name="payment_splits[${index}][amount]" step="0.01" min="0.01" required
                           placeholder="0.00" class="amount-input w-full pl-7 pr-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm font-mono">
                </div>
            </div>
            <button type="button" class="remove-split mt-5 w-8 h-8 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-rose-50 flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>`;
        div.querySelector('.amount-input').addEventListener('input', recalc);
        return div;
    }

    // ── Re-index rows ─────────────────────────────────────────────────
    function reIndex(container, namePrefix, fieldNames) {
        container.querySelectorAll(':scope > div').forEach((row, i) => {
            fieldNames.forEach(f => {
                const el = row.querySelector(`[name*="${f}"]`);
                if (el) el.name = `${namePrefix}[${i}][${f}]`;
            });
            // First row: hide remove btn
            const btn = row.querySelector('.remove-invoice, .remove-split');
            if (btn) {
                if (i === 0) { btn.classList.add('opacity-0', 'pointer-events-none'); }
                else         { btn.classList.remove('opacity-0', 'pointer-events-none'); }
            }
        });
    }

    // ── Recalculate totals ────────────────────────────────────────────
    function recalc() {
        let invSum = 0, paySum = 0;
        invoicesContainer.querySelectorAll('.inv-amount').forEach(el => invSum += parseFloat(el.value) || 0);
        splitsContainer.querySelectorAll('.amount-input').forEach(el => paySum += parseFloat(el.value) || 0);

        invTotalEl.textContent  = '$' + invSum.toFixed(2);
        payTotalEl.textContent  = '$' + paySum.toFixed(2);
        balInvEl.textContent    = '$' + invSum.toFixed(2);
        balPayEl.textContent    = '$' + paySum.toFixed(2);

        const diff = paySum - invSum;
        balDiffEl.textContent = (diff >= 0 ? '+' : '') + '$' + diff.toFixed(2);

        if (Math.abs(diff) < 0.01) {
            balBadge.className = 'text-sm font-semibold px-3 py-1 rounded-full border transition bg-emerald-100 text-emerald-800 border-emerald-300';
        } else if (diff > 0) {
            balBadge.className = 'text-sm font-semibold px-3 py-1 rounded-full border transition bg-sky-100 text-sky-800 border-sky-300';
        } else {
            balBadge.className = 'text-sm font-semibold px-3 py-1 rounded-full border transition bg-rose-100 text-rose-800 border-rose-300';
        }
    }

    // ── Add Invoice row ───────────────────────────────────────────────
    addInvoiceBtn.addEventListener('click', () => {
        const row = buildInvoiceRow(invCount++);
        invoicesContainer.appendChild(row);
        reIndex(invoicesContainer, 'invoice_items', ['id', 'amount']);
        row.querySelector('.invoice-select').focus();
    });

    // ── Add Payment split row ─────────────────────────────────────────
    addSplitBtn.addEventListener('click', () => {
        const row = buildSplitRow(payCount++);
        splitsContainer.appendChild(row);
        reIndex(splitsContainer, 'payment_splits', ['method', 'amount']);
        row.querySelector('.amount-input').focus();
    });

    // ── Remove rows (delegated) ───────────────────────────────────────
    invoicesContainer.addEventListener('click', e => {
        const btn = e.target.closest('.remove-invoice');
        if (!btn) return;
        btn.closest('.invoice-row').remove();
        reIndex(invoicesContainer, 'invoice_items', ['id', 'amount']);
        recalc();
    });

    splitsContainer.addEventListener('click', e => {
        const btn = e.target.closest('.remove-split');
        if (!btn) return;
        btn.closest('.split-row').remove();
        reIndex(splitsContainer, 'payment_splits', ['method', 'amount']);
        recalc();
    });

    // ── Auto-fill first invoice row amount when invoice changes ───────
    invoicesContainer.addEventListener('change', e => {
        if (!e.target.classList.contains('invoice-select')) return;
        const opt = e.target.options[e.target.selectedIndex];
        const amt = parseFloat(opt.dataset.amount || 0);
        const row = e.target.closest('.invoice-row');
        const amtInput = row.querySelector('.inv-amount');
        if (amtInput && amt > 0) amtInput.value = amt.toFixed(2);
        recalc();
    });

    invoicesContainer.addEventListener('input', e => {
        if (e.target.classList.contains('inv-amount')) recalc();
    });
    splitsContainer.addEventListener('input', e => {
        if (e.target.classList.contains('amount-input')) recalc();
    });

    recalc();
})();
</script>
@endsection
