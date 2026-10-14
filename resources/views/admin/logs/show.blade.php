@extends('layout.app')

@section('page_title', 'System Log #' . $log->id)
@section('page_description', 'Detailed telemetry, execution context, and stack trace')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('logs.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to System Logs
        </a>

        <form action="{{ route('logs.destroy', $log) }}" method="POST" onsubmit="return confirm('Delete this log entry?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Log Entry
            </button>
        </form>
    </div>

    <!-- Header Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-3 mb-2">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider border {{ $log->badge_class }}">
                {{ $log->level }}
            </span>
            <span class="text-xs text-slate-400">Recorded on {{ $log->created_at ? $log->created_at->format('M d, Y H:i:s') : '—' }} ({{ $log->created_at ? $log->created_at->diffForHumans() : '' }})</span>
        </div>
        <h2 class="text-xl font-bold text-slate-900 font-mono break-all">{{ $log->exception ?: 'Log #' . $log->id }}</h2>
        <div class="mt-3 p-4 bg-slate-50 rounded-xl border border-slate-200 text-sm font-mono text-slate-800 break-words">
            {{ $log->message }}
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Source File -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Source Location</h4>
            <div class="text-xs font-mono text-slate-800 break-all">
                {{ $log->file ?: '—' }}
            </div>
            @if ($log->line)
                <div class="mt-2 inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-xs">
                    Line {{ $log->line }}
                </div>
            @endif
        </div>

        <!-- HTTP Request -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">HTTP Request</h4>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-bold text-xs uppercase">{{ $log->method ?: 'GET' }}</span>
                <span class="text-xs font-mono text-indigo-600 font-semibold">{{ $log->route_name ?: 'Unnamed Route' }}</span>
            </div>
            <div class="text-xs font-mono text-slate-600 break-all">
                {{ $log->url ?: '—' }}
            </div>
        </div>

        <!-- Created By -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Created By</h4>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold uppercase shrink-0">
                    {{ strtoupper(substr($log->user ?? 'G', 0, 1)) }}
                </div>
                <div>
                    <div class="text-sm font-bold text-slate-900">
                        {{ $log->user ?: 'Guest / System' }}
                    </div>
                    @if ($log->creator)
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide
                                {{ match($log->creator->role) {
                                    'Super Admin' => 'bg-purple-100 text-purple-700',
                                    'Admin'       => 'bg-indigo-100 text-indigo-700',
                                    'Sales'       => 'bg-emerald-100 text-emerald-700',
                                    'Accountant'  => 'bg-amber-100 text-amber-700',
                                    default       => 'bg-slate-100 text-slate-600',
                                } }}">
                                {{ $log->creator->role }}
                            </span>
                            <span class="text-xs text-slate-500 font-mono">{{ $log->creator->email }}</span>
                        </div>
                    @elseif ($log->user_id)
                        <div class="text-xs text-slate-500 mt-0.5">User ID #{{ $log->user_id }}</div>
                    @else
                        <div class="text-xs text-slate-400 mt-0.5">Unauthenticated / System Event</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Company & Client IP -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Company & Client IP</h4>
            <div class="text-sm font-semibold text-slate-800">
                {{ $log->company ?: 'No Company Associated' }}
            </div>
            <div class="text-xs font-mono text-slate-500 mt-1">
                IP: {{ $log->ip ?: '127.0.0.1' }}
            </div>
        </div>
    </div>

    <!-- Stack Trace -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Stack Trace</h4>
        <div class="bg-slate-900 rounded-xl p-4 overflow-x-auto border border-slate-800 max-h-96">
            <pre class="font-mono text-xs text-slate-300 whitespace-pre leading-relaxed select-all">{{ $log->stack_trace ?: 'No stack trace captured.' }}</pre>
        </div>
    </div>
</div>
@endsection
