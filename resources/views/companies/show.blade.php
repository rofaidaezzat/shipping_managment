@extends('layout.app')

@section('title', $company->name . ' - CargoPort')
@section('page_title', $company->name)
@section('page_description', 'Company details and associated ports')

@section('content')
<div class="space-y-6">

    <!-- Header / Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('companies.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Companies
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('companies.edit', $company) }}" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                Edit Company
            </a>
            <a href="{{ route('ports.create') }}?company_id={{ $company->id }}" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                + Add Port to {{ $company->code }}
            </a>
        </div>
    </div>

    <!-- Company Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Company Name</span>
                <p class="text-lg font-bold text-slate-900 mt-0.5">{{ $company->name }}</p>
            </div>
            <div>
                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Unique Code</span>
                <p class="mt-0.5">
                    <span class="inline-block px-2.5 py-1 rounded bg-slate-100 text-slate-800 font-mono text-xs font-bold">
                        {{ $company->code }}
                    </span>
                </p>
            </div>
            <div>
                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Status</span>
                <p class="mt-0.5">
                    @if ($company->is_active)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Inactive
                        </span>
                    @endif
                </p>
            </div>
            <div>
                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Total Ports</span>
                <p class="text-lg font-bold text-indigo-600 mt-0.5">{{ $company->ports->count() }} Ports</p>
            </div>
        </div>
    </div>

    <!-- Ports belonging to this Company -->
    <div class="space-y-3">
        <h4 class="text-base font-bold text-slate-900">Associated Ports (1:M Relationship)</h4>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-6">Port Name</th>
                        <th class="py-3 px-6">Port Code (Unique per company)</th>
                        <th class="py-3 px-6">Containers Inside</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($company->ports as $port)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-6 font-semibold text-slate-900">
                                <a href="{{ route('ports.show', $port) }}" class="hover:text-indigo-600">
                                    {{ $port->name }}
                                </a>
                            </td>
                            <td class="py-3.5 px-6 font-mono text-xs font-semibold text-slate-700">
                                {{ $port->code }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $port->containers->count() }} Containers
                                </span>
                            </td>
                            <td class="py-3.5 px-6">
                                @if ($port->is_active)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('ports.show', $port) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                                    View Port &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                No ports registered for this company yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
