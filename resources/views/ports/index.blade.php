@extends('layout.app')

@section('title', 'Ports - CargoPort')
@section('page_title', 'Ports Management')
@section('page_description', 'Shipping terminal ports and composite unique codes')

@section('content')
<div class="space-y-6">

    <!-- Action Bar -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-slate-800">All Ports</h3>
            <p class="text-sm text-slate-500">Composite unique rule: Port Code is unique per Company (e.g. ALX can exist in Maersk and MSC).</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Export Button with Loading Spinner --}}
            <button id="export-ports-btn" type="button"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium shadow-sm transition disabled:opacity-75 disabled:cursor-not-allowed">
                {{-- Default Excel Download Icon --}}
                <svg id="export-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                {{-- Loading Spinner (hidden by default) --}}
                <svg id="export-spinner" class="hidden animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="export-btn-text">Export to Excel</span>
            </button>

            <a href="{{ route('ports.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Port
            </a>
        </div>
    </div>

    <!-- Ports Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-6">ID</th>
                    <th class="py-3.5 px-6">Port Name</th>
                    <th class="py-3.5 px-6">Port Code</th>
                    <th class="py-3.5 px-6">Parent Company</th>
                    <th class="py-3.5 px-6">Containers</th>
                    <th class="py-3.5 px-6">Status</th>
                    <th class="py-3.5 px-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse ($ports as $port)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="py-4 px-6 font-mono text-slate-500 text-xs">#{{ $port->id }}</td>
                        <td class="py-4 px-6 font-semibold text-slate-900">
                            <a href="{{ route('ports.show', $port) }}" class="hover:text-indigo-600 transition">
                                {{ $port->name }}
                            </a>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-block px-2.5 py-1 rounded bg-slate-100 text-slate-700 font-mono text-xs font-semibold">
                                {{ $port->code }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <a href="{{ route('companies.show', $port->company) }}" class="inline-flex items-center gap-1.5 font-medium text-slate-800 hover:text-indigo-600">
                                <span>{{ $port->company->name }}</span>
                                <span class="text-xs font-mono text-slate-400">({{ $port->company->code }})</span>
                            </a>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $port->containers_count > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-600' }}">
                                {{ $port->containers_count }} Containers
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            @if ($port->is_active)
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
                                <a href="{{ route('ports.show', $port) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 transition" title="View details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('ports.edit', $port) }}" class="p-1.5 text-slate-400 hover:text-amber-600 transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('ports.destroy', $port) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? Ports with containers cannot be deleted.');">
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
                        <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                            No ports found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

   

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const exportBtn = document.getElementById('export-ports-btn');
    const exportIcon = document.getElementById('export-icon');
    const exportSpinner = document.getElementById('export-spinner');
    const exportText = document.getElementById('export-btn-text');

    if (!exportBtn) return;

    exportBtn.addEventListener('click', function () {
        // 1. Show Loading Spinner on button and disable it
        exportBtn.disabled = true;
        exportIcon.classList.add('hidden');
        exportSpinner.classList.remove('hidden');
        exportText.textContent = 'Processing Export...';

        // 2. Request Excel export via AJAX (Fetch) - triggers ProcessDataJob on server
        fetch("{{ route('ports.export') }}")
            .then(response => {
                if (!response.ok) {
                    throw new Error('Export request failed: ' + response.statusText);
                }
                const disposition = response.headers.get('content-disposition');
                let filename = 'ports.xlsx';
                if (disposition && disposition.includes('filename=')) {
                    const match = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (match && match[1]) {
                        filename = match[1].replace(/['"]/g, '').trim();
                    }
                }
                return response.blob().then(blob => ({ blob, filename }));
            })
            .then(({ blob, filename }) => {
                // 3. Trigger download of the exported file
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

                // 4. Temporary success state before reset
                exportSpinner.classList.add('hidden');
                exportIcon.classList.remove('hidden');
                exportText.textContent = 'Downloaded!';
                setTimeout(() => {
                    exportBtn.disabled = false;
                    exportText.textContent = 'Export to Excel';
                }, 2000);
            })
            .catch(error => {
                console.error('Export error:', error);
                alert('Export failed. Please check the system log.');
                exportSpinner.classList.add('hidden');
                exportIcon.classList.remove('hidden');
                exportBtn.disabled = false;
                exportText.textContent = 'Export to Excel';
            });
    });
});
</script>
@endsection
