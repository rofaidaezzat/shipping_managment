@extends('layout.app')

@section('title', 'Add Container - CargoPort')
@section('page_title', 'Create Container')
@section('page_description', 'Register a new shipping container')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('containers.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Containers
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <h3 class="text-lg font-bold text-slate-900 mb-1">Container Details</h3>
        <p class="text-xs text-slate-500 mb-6">
            Container number must be globally unique. Leave port empty for an unassigned (floating) container.
        </p>

        <form action="{{ route('containers.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Container Number --}}
            <div>
                <label for="container_number" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Container Number (Unique) <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="container_number" id="container_number"
                       value="{{ old('container_number') }}"
                       placeholder="e.g. MSKU1234567" maxlength="20" required
                       class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('container_number') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm font-mono uppercase">
                <p class="text-[11px] text-slate-400 mt-1">Must be unique across all containers. Max 20 characters.</p>
                @error('container_number')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Type (ENUM) --}}
            <div>
                <label for="type" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Container Type <span class="text-rose-500">*</span>
                </label>
                <select name="type" id="type" required
                        class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('type') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">-- Select Type --</option>
                    <option value="20GP" {{ old('type') === '20GP' ? 'selected' : '' }}>20GP — 20ft Standard</option>
                    <option value="40GP" {{ old('type') === '40GP' ? 'selected' : '' }}>40GP — 40ft Standard</option>
                    <option value="40HC" {{ old('type') === '40HC' ? 'selected' : '' }}>40HC — 40ft High Cube</option>
                </select>
                @error('type')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status (ENUM) --}}
            <div>
                <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                    Status <span class="text-rose-500">*</span>
                </label>
                <select name="status" id="status" required
                        class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('status') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                    <option value="">-- Select Status --</option>
                    <option value="AVAILABLE"   {{ old('status') === 'AVAILABLE'   ? 'selected' : '' }}>AVAILABLE — Ready to use</option>
                    <option value="IN_USE"      {{ old('status') === 'IN_USE'      ? 'selected' : '' }}>IN_USE — Currently in service</option>
                    <option value="MAINTENANCE" {{ old('status') === 'MAINTENANCE' ? 'selected' : '' }}>MAINTENANCE — Under repair</option>
                </select>
                @error('status')
                    <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            @php
                $portCountries = $ports->pluck('country')->filter()->unique()->sort()->values();
                $selectedPortId = old('port_id', request('port_id'));
                $selectedPort = $ports->firstWhere('id', $selectedPortId);
                $initialCountry = old('country', $selectedPort?->country ?? '');
            @endphp

            {{-- Port & Country Assignment (Nullable FK) --}}
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Port Assignment</span>
                        <p class="text-[11px] text-slate-500 mt-0.5">Select a country to view its ports, or leave unassigned.</p>
                    </div>
                    <span id="country-port-badge" class="hidden text-[11px] font-semibold px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Country Filter --}}
                    <div>
                        <label for="country_select" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Country
                        </label>
                        <select id="country_select" name="country"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                            <option value="">-- Select Country --</option>
                            <option value="__all__" {{ $initialCountry === '__all__' ? 'selected' : '' }}>-- All Countries (Show All) --</option>
                            @foreach ($portCountries as $country)
                                <option value="{{ $country }}" {{ $initialCountry === $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Filters available ports by country.</p>
                    </div>

                    {{-- Assigned Port --}}
                    <div>
                        <label for="port_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">
                            Assigned Port <span class="text-slate-400 font-normal">(Optional)</span>
                        </label>
                        <select name="port_id" id="port_id"
                                class="w-full px-3.5 py-2.5 rounded-lg border {{ $errors->has('port_id') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm bg-white">
                            <option value="">-- Unassigned (port_id = NULL) --</option>
                            @foreach ($ports as $port)
                                <option value="{{ $port->id }}"
                                    data-country="{{ $port->country }}"
                                    {{ (string)$selectedPortId === (string)$port->id ? 'selected' : '' }}>
                                    {{ $port->name }} ({{ $port->code }}) — {{ $port->company->name ?? '—' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">FK: port_id → ports.id (RESTRICT).</p>
                        @error('port_id')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- is_active --}}
            <div class="pt-2">
                <input type="hidden" name="is_active" value="0">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <span class="text-sm font-medium text-slate-700">Container is Active</span>
                </label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('containers.index') }}"
                   class="px-4 py-2 border border-slate-300 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-medium transition">
                    Cancel
                </a>
                <button type="submit"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition">
                    Save Container
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const countrySelect = document.getElementById('country_select');
    const portSelect = document.getElementById('port_id');
    const badge = document.getElementById('country-port-badge');

    if (!countrySelect || !portSelect) return;

    // Cache the original port options
    const unassignedOption = portSelect.options[0];
    const allPortOptions = Array.from(portSelect.querySelectorAll('option')).slice(1);
    const initialPortId = @json((string)$selectedPortId);

    function updatePorts(selectedCountry, keepValue = true) {
        const previousValue = keepValue ? (portSelect.value || initialPortId) : '';

        // Reset dropdown
        portSelect.innerHTML = '';
        portSelect.appendChild(unassignedOption.cloneNode(true));

        if (!selectedCountry) {
            if (badge) badge.classList.add('hidden');
            const promptOpt = document.createElement('option');
            promptOpt.value = '';
            promptOpt.disabled = true;
            promptOpt.textContent = '← Please select a country first';
            portSelect.appendChild(promptOpt);
            portSelect.value = '';
            return;
        }

        let matches = [];
        if (selectedCountry === '__all__') {
            matches = allPortOptions;
            if (badge) {
                badge.textContent = `${matches.length} total ports`;
                badge.classList.remove('hidden');
            }
        } else {
            matches = allPortOptions.filter(opt => opt.getAttribute('data-country') === selectedCountry);
            if (badge) {
                badge.textContent = `${matches.length} port${matches.length === 1 ? '' : 's'} in ${selectedCountry}`;
                badge.classList.remove('hidden');
            }
        }

        if (matches.length === 0) {
            const emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.disabled = true;
            emptyOpt.textContent = 'No ports available in this country';
            portSelect.appendChild(emptyOpt);
            portSelect.value = '';
        } else {
            matches.forEach(opt => {
                portSelect.appendChild(opt.cloneNode(true));
            });

            if (previousValue) {
                const found = Array.from(portSelect.options).some(o => o.value === String(previousValue));
                if (found) {
                    portSelect.value = String(previousValue);
                } else {
                    portSelect.value = '';
                }
            } else {
                portSelect.value = '';
            }
        }
    }

    countrySelect.addEventListener('change', function () {
        updatePorts(this.value, false);
    });

    // If page loaded with an existing selection
    updatePorts(countrySelect.value, true);
});
</script>
@endsection
