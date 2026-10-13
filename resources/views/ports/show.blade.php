@extends('layout.app')

@section('title', $port->name . ' - CargoPort')
@section('page_title', $port->name . ' (' . $port->code . ')')
@section('page_description', 'Port terminal overview and stored containers')

@section('content')
<div class="space-y-6">

    <!-- Header / Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('ports.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Ports
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('ports.edit', $port) }}" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                Edit Port
            </a>
            <a href="{{ route('containers.create') }}?port_id={{ $port->id }}" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                + Add Container to {{ $port->code }}
            </a>
        </div>
    </div>

    <!-- Port Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Port Name</span>
                <p class="text-lg font-bold text-slate-900 mt-0.5">{{ $port->name }}</p>
            </div>
            <div>
                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Port Code / Company</span>
                <p class="mt-0.5 flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-800 font-mono text-xs font-bold">
                        {{ $port->code }}
                    </span>
                    <span class="text-xs text-slate-500">
                        under <a href="{{ route('companies.show', $port->company) }}" class="text-indigo-600 font-medium hover:underline">{{ $port->company->name }}</a>
                    </span>
                </p>
            </div>
            <div>
                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Status</span>
                <p class="mt-0.5">
                    @if ($port->is_active)
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
                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Containers In Port</span>
                <p class="text-lg font-bold text-indigo-600 mt-0.5">{{ $port->containers->count() }} Units</p>
            </div>
        </div>
    </div>

    <!-- Containers at this Port -->
    <div class="space-y-3">
        <h4 class="text-base font-bold text-slate-900">Containers Stored at this Port (1:M Relationship)</h4>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-6">Container Number</th>
                        <th class="py-3 px-6">Type</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6">Active</th>
                        <th class="py-3 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($port->containers as $container)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3.5 px-6 font-mono font-bold text-slate-900">
                                {{ $container->container_number }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="px-2 py-0.5 rounded font-mono text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $container->type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6">
                                @if ($container->status === 'AVAILABLE')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">AVAILABLE</span>
                                @elseif ($container->status === 'IN_USE')
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">IN USE</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">MAINTENANCE</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6">
                                @if ($container->is_active)
                                    <span class="text-xs font-semibold text-emerald-600">Active</span>
                                @else
                                    <span class="text-xs font-semibold text-rose-500">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('containers.edit', $container) }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">
                                    Edit &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                No containers currently stationed in this port.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
