@extends('layout.app')
@section('title', 'Add New Port - CargoPort')
@section('page_title', 'Create Port')
@section('page_description', 'Register a new port with terminals')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Back Link --}}
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('ports.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Ports
        </a>
    </div>

    {{-- Tab Bar --}}
    <div class="mb-4 flex items-center gap-1">
        <a href="{{ route('ports.index') }}"
           class="px-5 py-2 rounded-md text-sm font-medium text-slate-500 hover:bg-slate-100 transition">
            Port &amp; Terminal List
        </a>
        <span class="px-5 py-2 rounded-md text-sm font-semibold bg-indigo-600 text-white shadow-sm">
            Add New Port
        </span>
    </div>

    {{-- Main Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

        <form action="{{ route('ports.store') }}" method="POST" id="create-port-form">
            @csrf

            {{-- Card Header --}}
            <div class="px-8 pt-6 pb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Port Details</h3>
                <p class="text-xs text-slate-400 mt-0.5">Port code must be unique within the selected company.</p>
            </div>

            {{-- Fields Grid --}}
            <div class="px-8 py-6 grid grid-cols-2 gap-5">

                {{-- Type --}}
                <div>
                    <label for="type" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                        Type <span class="text-rose-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('type') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                        <option value="">Select ...</option>
                        <option value="Sea"  {{ old('type') === 'Sea'  ? 'selected' : '' }}>🚢 Sea</option>
                        <option value="Air"  {{ old('type') === 'Air'  ? 'selected' : '' }}>✈️ Air</option>
                        <option value="Land" {{ old('type') === 'Land' ? 'selected' : '' }}>🚛 Land</option>
                    </select>
                    @error('type')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Company --}}
                <div>
                    <label for="company_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                        Shipping Company <span class="text-rose-500">*</span>
                    </label>
                    <select name="company_id" id="company_id" required
                            class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('company_id') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                        <option value="">Select ...</option>
                        @foreach ($companies as $comp)
                            <option value="{{ $comp->id }}" {{ old('company_id', request('company_id')) == $comp->id ? 'selected' : '' }}>
                                {{ $comp->name }} ({{ $comp->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Name (plain text) --}}
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                        Port Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name') }}"
                           placeholder="e.g. Alexandria Port"
                           maxlength="150" required
                           class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('name') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Code --}}
                <div>
                    <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                        Port Code <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="code" id="code"
                           value="{{ old('code') }}"
                           placeholder="e.g. ALX"
                           maxlength="20" required
                           class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('code') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-mono uppercase">
                    <p class="text-[11px] text-slate-400 mt-1">Unique per company.</p>
                    @error('code')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Country --}}
                <div>
                    <label for="country" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                        Country <span class="text-rose-500">*</span>
                    </label>
                    <select name="country" id="country" required
                            class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('country') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                        <option value="">Select ...</option>
                        @php
                            $countries = [
                                'Algeria','Argentina','Australia','Bahrain','Bangladesh',
                                'Belgium','Brazil','Canada','Chile','China','Colombia',
                                'Croatia','Egypt','Ethiopia','France','Germany','Ghana',
                                'Greece','India','Indonesia','Iraq','Italy','Japan',
                                'Jordan','Kenya','Kuwait','Lebanon','Libya','Malaysia',
                                'Mexico','Morocco','Netherlands','New Zealand','Nigeria',
                                'Oman','Pakistan','Peru','Philippines','Poland',
                                'Portugal','Qatar','Romania','Saudi Arabia','Singapore',
                                'South Africa','South Korea','Spain','Sri Lanka','Sudan',
                                'Tanzania','Thailand','Tunisia','Turkey',
                                'United Arab Emirates','United Kingdom','United States',
                                'Vietnam',
                            ];
                        @endphp
                        @foreach($countries as $c)
                            <option value="{{ $c }}" {{ old('country') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    @error('country')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Is Active --}}
                <div class="flex items-end pb-1">
                    <input type="hidden" name="is_active" value="0">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                        <span class="text-sm font-medium text-slate-700">Port is Active</span>
                    </label>
                </div>

            </div>

            {{-- Terminal Table Section --}}
            <div class="border-t border-slate-200 mx-8 mb-6 rounded-lg overflow-hidden shadow-sm">
                <table class="w-full text-left text-sm" id="terminal-table">
                    <thead>
                        <tr class="bg-indigo-600 text-white">
                            <th class="py-3 px-5 font-semibold tracking-wide text-xs uppercase w-1/2">Terminal Code</th>
                            <th class="py-3 px-5 font-semibold tracking-wide text-xs uppercase">Type</th>
                            <th class="py-3 px-3 w-12 text-right">
                                <button type="button" id="add-terminal-row"
                                        title="Add terminal row"
                                        class="w-7 h-7 bg-emerald-500 hover:bg-emerald-400 text-white rounded flex items-center justify-center transition font-bold text-base leading-none ml-auto">
                                    +
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="terminal-body" class="divide-y divide-slate-100">
                        <tr id="empty-row">
                            <td colspan="3" class="py-8 text-center text-slate-400 text-xs">
                                No terminals added yet. Click <strong>+</strong> to add.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Action Buttons --}}
            <div class="px-8 pb-7 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                <a href="{{ route('ports.index') }}"
                   class="px-5 py-2.5 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm transition">
                    Create Port
                </button>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addBtn   = document.getElementById('add-terminal-row');
    const tbody    = document.getElementById('terminal-body');
    const emptyRow = document.getElementById('empty-row');
    let   idx      = 0;

    addBtn.addEventListener('click', function () {
        emptyRow.style.display = 'none';

        const tr = document.createElement('tr');
        tr.className = 'bg-white hover:bg-slate-50 transition';
        tr.innerHTML = `
            <td class="py-2.5 px-5">
                <input type="text"
                       name="terminals[${idx}][code]"
                       placeholder="e.g. ALX-T1"
                       maxlength="50"
                       class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </td>
            <td class="py-2.5 px-5">
                <select name="terminals[${idx}][type]"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="Sea">🚢 Sea</option>
                    <option value="Air">✈️ Air</option>
                    <option value="Land">🚛 Land</option>
                </select>
            </td>
            <td class="py-2.5 px-3 text-right">
                <button type="button"
                        class="remove-row w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-500 text-rose-400 hover:text-white flex items-center justify-center transition text-lg font-bold ml-auto"
                        title="Remove row">&times;</button>
            </td>
        `;

        tr.querySelector('.remove-row').addEventListener('click', function () {
            tr.remove();
            if (!tbody.querySelector('tr:not(#empty-row)')) {
                emptyRow.style.display = '';
            }
        });

        tbody.appendChild(tr);
        idx++;
    });
});
</script>
@endsection
