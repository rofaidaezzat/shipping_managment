@extends('layout.app')

@section('title', 'Edit Company - CargoPort')
@section('page_title', 'Edit Company')
@section('page_description', 'Update company information')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Companies
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <h3 class="text-lg font-bold text-slate-900 mb-1">Edit Company: {{ $company->name }}</h3>
        <p class="text-xs text-slate-500 mb-6">Editing company #{{ $company->id }}</p>

        <form action="{{ route('companies.update', $company) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Company Name <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" required
                       class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('name') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                @error('name')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Code -->
            <div>
                <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Company Code (Unique) <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="code" id="code" value="{{ old('code', $company->code) }}" maxlength="20" required
                       class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('code') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-mono uppercase">
                @error('code')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- is_active -->
            <div class="pt-2">
                <input type="hidden" name="is_active" value="0">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $company->is_active) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <span class="text-sm font-medium text-slate-700">Company is Active</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('companies.index') }}" class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                    Update Company
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
