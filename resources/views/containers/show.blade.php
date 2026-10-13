@extends('layout.app')

@section('title', $container->container_number . ' - CargoPort')
@section('page_title', 'Container: ' . $container->container_number)
@section('page_description', 'Shipping container details and current assignment')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('containers.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Containers
        </a>
        <a href="{{ route('containers.edit', $container) }}"
           class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-semibold shadow-sm transition">
            Edit Container
        </a>
    </div>

    {{-- Container Info Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Container Number</span>
                <p class="text-xl font-bold font-mono text-slate-900 mt-0.5">{{ $container->container_number }}</p>
            </div>
            <div>
                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Type (ENUM)</span>
                <p class="mt-1">
                    <span class="inline-block px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-mono text-sm font-bold border border-indigo-200">
                        {{ $container->type }}
                    </span>
                </p>
            </div>
            <div>
                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Status (ENUM)</span>
                <p class="mt-1">
                    @if ($container->status === 'AVAILABLE')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> AVAILABLE
                        </span>
                    @elseif ($container->status === 'IN_USE')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> IN_USE
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> MAINTENANCE
                        </span>
                    @endif
                </p>
            </div>
            <div>
                <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Active</span>
                <p class="mt-1">
                    @if ($container->is_active)
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
        </div>
    </div>

    {{-- Port Assignment Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h4 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Port Assignment (FK: port_id)</h4>
        @if ($container->port)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Port</span>
                    <p class="mt-0.5 font-semibold text-slate-900">
                        <a href="{{ route('ports.show', $container->port) }}" class="hover:text-indigo-600">
                            {{ $container->port->name }}
                        </a>
                        <span class="text-xs font-mono text-slate-400 ml-1">({{ $container->port->code }})</span>
                    </p>
                </div>
                <div>
                    <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Shipping Company</span>
                    <p class="mt-0.5 font-semibold text-slate-900">
                        @if ($container->port->company)
                            <a href="{{ route('companies.show', $container->port->company) }}" class="hover:text-indigo-600">
                                {{ $container->port->company->name }}
                            </a>
                            <span class="text-xs font-mono text-slate-400 ml-1">({{ $container->port->company->code }})</span>
                        @endif
                    </p>
                </div>
                <div>
                    <span class="text-xs uppercase tracking-wider font-semibold text-slate-400">Port Status</span>
                    <p class="mt-1">
                        @if ($container->port->is_active)
                            <span class="text-xs font-semibold text-emerald-600">Active Port</span>
                        @else
                            <span class="text-xs font-semibold text-rose-500">Inactive Port</span>
                        @endif
                    </p>
                </div>
            </div>
        @else
            <div class="flex items-center gap-3 p-4 rounded-lg bg-slate-50 border border-dashed border-slate-300">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-slate-600">Unassigned Container</p>
                    <p class="text-xs text-slate-400"><code class="bg-slate-100 px-1 rounded">port_id = NULL</code> — This container is not assigned to any port yet.</p>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
