@extends('layout.app')

@section('title', 'Containers - CargoPort')
@section('page_title', 'Containers Inventory')
@section('page_description', 'Shipping container fleet and port allocations')

@section('content')
<div class="space-y-6">

    <!-- Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-slate-800">All Containers</h3>
            <p class="text-sm text-slate-500">Includes assigned containers and unassigned/floating containers (port_id = NULL).</p>
        </div>
        <a href="{{ route('containers.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Container
        </a>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Fleet</span>
            <p class="text-2xl font-bold text-slate-900 mt-1">{{ $containers->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Available</span>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $containers->where('status', 'AVAILABLE')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">In Use</span>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $containers->where('status', 'IN_USE')->count() }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Maintenance</span>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $containers->where('status', 'MAINTENANCE')->count() }}</p>
        </div>
    </div>

    <!-- Containers Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-6">ID</th>
                    <th class="py-3.5 px-6">Container Number</th>
                    <th class="py-3.5 px-6">Type</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6">Assigned Port / Line</th>
                    <th class="py-3.5 px-6">Active</th>
                    <th class="py-3.5 px-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($containers as $container)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-4 px-6 font-mono text-slate-500 text-xs">#{{ $container->id }}</td>
                        <td class="py-4 px-6 font-mono font-bold text-slate-900">
                            {{ $container->container_number }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-block px-2.5 py-1 rounded bg-indigo-50 text-indigo-700 font-mono text-xs font-bold border border-indigo-200">
                                {{ $container->type }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            @if ($container->status === 'AVAILABLE')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> AVAILABLE
                                </span>
                            @elseif ($container->status === 'IN_USE')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> IN_USE
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> MAINTENANCE
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @if ($container->port)
                                <a href="{{ route('ports.show', $container->port) }}" class="font-medium text-slate-800 hover:text-indigo-600">
                                    {{ $container->port->name }}
                                    <span class="text-xs font-mono text-slate-400">({{ $container->port->code }})</span>
                                </a>
                                @if ($container->port->company)
                                    <span class="block text-xs text-slate-400">via {{ $container->port->company->name }}</span>
                                @endif
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-dashed border-slate-300">
                                    Unassigned (NULL)
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @if ($container->is_active)
                                <span class="text-xs font-semibold text-emerald-600">Active</span>
                            @else
                                <span class="text-xs font-semibold text-rose-500">Inactive</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="inline-flex items-center gap-2">
                                <a href="{{ route('containers.edit', $container) }}" class="p-1.5 text-slate-400 hover:text-amber-600 transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('containers.destroy', $container) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete container {{ $container->container_number }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 transition" title="Delete">
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
                        <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                            No containers registered.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
