@extends('layout.app')

@section('title', 'Shipping Companies - CargoPort')
@section('page_title', 'Shipping Companies')
@section('page_description', 'Manage shipping lines and parent entities')

@section('content')
<div class="space-y-6">

    <!-- Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-slate-800">All Companies</h3>
            <p class="text-sm text-slate-500">Global unique code requirement: each company has a unique code.</p>
        </div>
        <a href="{{ route('companies.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Company
        </a>
    </div>

    <!-- Companies Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-6">ID</th>
                    <th class="py-3.5 px-6">Company Name</th>
                    <th class="py-3.5 px-6">Code (Unique)</th>
                    <th class="py-3.5 px-6">Ports Count</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($companies as $company)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-4 px-6 font-mono text-slate-500 text-xs">#{{ $company->id }}</td>
                        <td class="py-4 px-6 font-semibold text-slate-900">
                            <a href="{{ route('companies.show', $company) }}" class="hover:text-indigo-600 transition">
                                {{ $company->name }}
                            </a>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-block px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-mono text-xs font-semibold">
                                {{ $company->code }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $company->ports_count > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-600' }}">
                                {{ $company->ports_count }} Ports
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            @if ($company->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('companies.show', $company) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 transition" title="View details & ports">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('companies.edit', $company) }}" class="p-1.5 text-slate-400 hover:text-amber-600 transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('companies.destroy', $company) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? Deletion will be rejected if this company has associated ports.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Delete (RESTRICT Protected)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 text-sm">
                            No companies found. Click "Add New Company" or seed the database.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    

</div>
@endsection
