@extends('layout.app')

@section('page_title', 'Activity #' . $activity->id)
@section('page_description', 'Transaction audit detail and attribute changes')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('activity_logs.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Activity Logs
        </a>

        <span class="text-xs text-slate-400">
            Recorded on {{ $activity->created_at?->format('M d, Y H:i:s') }}
        </span>
    </div>

    @php
        $event = strtolower($activity->event ?? 'action');
        $eventBadge = match($event) {
            'created' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'updated' => 'bg-amber-100 text-amber-800 border-amber-200',
            'deleted' => 'bg-rose-100 text-rose-800 border-rose-200',
            default   => 'bg-indigo-100 text-indigo-800 border-indigo-200',
        };
        $subjectClass = class_basename($activity->subject_type ?? 'Record');
        $changes = $activity->changes();
        $attributes = $changes['attributes'] ?? [];
        $old = $changes['old'] ?? [];
        $causer = $activity->causer;
    @endphp

    <!-- Header Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-3 mb-2">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider border {{ $eventBadge }}">
                {{ $activity->event ?? 'ACTION' }}
            </span>
            <span class="text-xs text-slate-500 font-mono">
                Log Name: {{ $activity->log_name ?? 'default' }}
            </span>
        </div>
        <h2 class="text-lg font-bold text-slate-900 mt-1">{{ $activity->description }}</h2>
    </div>

    <!-- Grid Context -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Created By -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Created By</h4>
            @php
                $causer = $activity->creator ?? $activity->causer;
            @endphp
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm uppercase shrink-0">
                    {{ strtoupper(substr($causer?->name ?? 'S', 0, 1)) }}
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-900">{{ $causer?->name ?? 'System Process' }}</div>
                    @if ($causer)
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-indigo-100 text-indigo-700">
                                {{ $causer->role }}
                            </span>
                            <span class="text-xs text-slate-500 font-mono">{{ $causer->email }}</span>
                        </div>
                    @else
                        <div class="text-xs text-slate-400 mt-0.5">Automated System Action</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Target Subject -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Target Subject</h4>
            <div class="text-sm font-bold text-slate-900">
                {{ $subjectClass }} #{{ $activity->subject_id ?? '—' }}
            </div>
            <div class="text-xs text-slate-500 font-mono mt-1">
                Class: {{ $activity->subject_type ?? '—' }}
            </div>
        </div>
    </div>

    <!-- Changes Diff Table -->
    @php
        $allKeys = array_unique(array_merge(array_keys($attributes), array_keys($old)));
    @endphp
    @if (!empty($allKeys))
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Field-by-Field Modifications</h4>
            <div class="overflow-hidden rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100/80 text-[10px] font-bold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                            <th class="py-2.5 px-3">Field</th>
                            <th class="py-2.5 px-3">Old Value</th>
                            <th class="py-2.5 px-3">New Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                        @foreach ($allKeys as $key)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-2.5 px-3 font-semibold text-slate-700 bg-slate-50/50">{{ $key }}</td>
                                <td class="py-2.5 px-3 text-rose-700 bg-rose-50/20">
                                    {{ isset($old[$key]) ? json_encode($old[$key]) : '—' }}
                                </td>
                                <td class="py-2.5 px-3 text-emerald-700 bg-emerald-50/20">
                                    {{ isset($attributes[$key]) ? json_encode($attributes[$key]) : '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Raw Properties JSON -->
    @if (!empty($activity->properties))
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Stored Properties JSON</h4>
            <div class="bg-slate-900 rounded-xl p-4 overflow-x-auto border border-slate-800">
                <pre class="font-mono text-xs text-slate-300 whitespace-pre leading-relaxed select-all">{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    @endif
</div>
@endsection
