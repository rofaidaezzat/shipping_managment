@extends('layout.app')

@section('title', 'Edit Port - CargoPort')
@section('page_title', 'Edit Port')
@section('page_description', 'Update port information')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('ports.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Ports
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <h3 class="text-lg font-bold text-slate-900 mb-1">Edit Port: {{ $port->name }}</h3>
        <p class="text-xs text-slate-500 mb-6">Editing port #{{ $port->id }}</p>

        <form action="{{ route('ports.update', $port) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Type -->
                <div>
                    <label for="type" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Type <span class="text-rose-500">*</span>
                    </label>
                    <select name="type" id="type" required
                            class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('type') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                        <option value="Sea"  {{ old('type', $port->type ?? 'Sea') === 'Sea'  ? 'selected' : '' }}>🚢 Sea</option>
                        <option value="Air"  {{ old('type', $port->type) === 'Air'  ? 'selected' : '' }}>✈️ Air</option>
                        <option value="Land" {{ old('type', $port->type) === 'Land' ? 'selected' : '' }}>🚛 Land</option>
                    </select>
                    @error('type')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company Select -->
                <div>
                    <label for="company_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Shipping Company <span class="text-rose-500">*</span>
                    </label>
                    <select name="company_id" id="company_id" required
                            class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('company_id') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                        @foreach ($companies as $comp)
                            <option value="{{ $comp->id }}" {{ old('company_id', $port->company_id) == $comp->id ? 'selected' : '' }}>
                                {{ $comp->name }} ({{ $comp->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Port Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $port->name) }}" required maxlength="150"
                       class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('name') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Code -->
                <div>
                    <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Port Code (Composite Unique) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code', $port->code) }}" maxlength="20" required
                           class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('code') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-mono uppercase">
                    <p class="text-[11px] text-slate-400 mt-1">Unique per company.</p>
                    @error('code')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Country -->
                <div>
                    <label for="country" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                        Country
                    </label>
                    <select name="country" id="country"
                            class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('country') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                        <option value="">Select ...</option>
                        @php
                            $countryList = [
                                'Algeria','Argentina','Australia','Bahrain','Bangladesh',
                                'Belgium','Brazil','Canada','Chile','China','Colombia',
                                'Croatia','Egypt','Ethiopia','France','Germany','Ghana',
                                'Greece','India','Indonesia','Iraq','Italy','Japan',
                                'Jordan','Kenya','Kuwait','Lebanon','Libya','Malaysia',
                                'Mexico','Morocco','Netherlands','New Zealand','Nigeria',
                                'Oman','Pakistan','Peru','Philippines','Poland',
                                'Portugal','Qatar','Saudi Arabia','Singapore',
                                'South Africa','South Korea','Spain','Sri Lanka','Sudan',
                                'Tanzania','Thailand','Tunisia','Turkey',
                                'United Arab Emirates','United Kingdom','United States',
                                'Vietnam',
                            ];
                        @endphp
                        @foreach($countryList as $c)
                            <option value="{{ $c }}" {{ old('country', $port->country) === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    @error('country')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- is_active -->
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $port->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <span class="text-sm font-medium text-slate-700">Port is Active</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('ports.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                    Update Port
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
