@extends('layout.app')

@section('page_title', 'System Logs')
@section('page_description', 'System error tracking, exception inspection & application telemetry')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Breadcrumb Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-1">
                <span class="inline-flex items-center gap-1 text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    System
                </span>
                <span>/</span>
                <span class="text-indigo-600 font-semibold">Logs</span>
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    <svg class="w-3 h-3 mr-1 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
                    </svg>
                    Super Admin Only
                </span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">System Event & Error Logs</h1>
        </div>

        <div class="flex items-center flex-wrap gap-2.5">
            <!-- Test Log Trigger Menu -->
            <div class="relative inline-block text-left" id="testDropdownWrapper">
                <button type="button" 
                        onclick="document.getElementById('testDropdown').classList.toggle('hidden')" 
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 transition shadow-sm">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Test Event</span>
                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="testDropdown" class="hidden absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-xl border border-slate-200 z-50 py-1.5 text-xs">
                    <a href="{{ route('logs.test', ['type' => 'exception']) }}" class="flex items-center gap-2 px-3.5 py-2 text-rose-700 hover:bg-rose-50 transition">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Trigger Test Exception
                    </a>
                    <a href="{{ route('logs.test', ['type' => 'warning']) }}" class="flex items-center gap-2 px-3.5 py-2 text-amber-700 hover:bg-amber-50 transition">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Record Test Warning
                    </a>
                    <a href="{{ route('logs.test', ['type' => 'info']) }}" class="flex items-center gap-2 px-3.5 py-2 text-sky-700 hover:bg-sky-50 transition">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        Record Test Info
                    </a>
                </div>
            </div>

            <!-- Clear All Logs Button -->
            @if ($metrics['total'] > 0)
                <button type="button" onclick="confirmClearLogs()" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition shadow-sm">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Clear All Logs</span>
                </button>

                <form id="clearLogsForm" action="{{ route('logs.clear') }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif

            <!-- Refresh Button -->
            <a href="{{ route('logs.index') }}" class="p-2 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition shadow-sm" title="Refresh">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Logs -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Recorded</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($metrics['total']) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Application events & exceptions</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        <!-- Errors & Critical -->
        <div class="bg-white p-5 rounded-2xl border border-rose-100 shadow-sm flex items-center justify-between bg-gradient-to-br from-white to-rose-50/30">
            <div>
                <p class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Errors & Critical</p>
                <h3 class="text-2xl font-black text-rose-700 mt-1">{{ number_format($metrics['errors']) }}</h3>
                <p class="text-[11px] text-rose-500 mt-0.5">Exceptions needing attention</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>

        <!-- Warnings -->
        <div class="bg-white p-5 rounded-2xl border border-amber-100 shadow-sm flex items-center justify-between bg-gradient-to-br from-white to-amber-50/30">
            <div>
                <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Warnings</p>
                <h3 class="text-2xl font-black text-amber-700 mt-1">{{ number_format($metrics['warnings']) }}</h3>
                <p class="text-[11px] text-amber-500 mt-0.5">Non-blocking alerts</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        <!-- Info & Debug -->
        <div class="bg-white p-5 rounded-2xl border border-sky-100 shadow-sm flex items-center justify-between bg-gradient-to-br from-white to-sky-50/30">
            <div>
                <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider">Info & Debug</p>
                <h3 class="text-2xl font-black text-sky-700 mt-1">{{ number_format($metrics['info']) }}</h3>
                <p class="text-[11px] text-sky-500 mt-0.5">System operations</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('logs.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end">
            <!-- Search Text -->
            <div class="md:col-span-5">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Search Logs</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search message, exception, file, route, user, IP..." 
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Level Dropdown -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Log Level</label>
                <select name="level" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">All Levels</option>
                    <option value="emergency" {{ request('level') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                    <option value="critical" {{ request('level') === 'critical' ? 'selected' : '' }}>Critical</option>
                    <option value="error" {{ request('level') === 'error' ? 'selected' : '' }}>Error</option>
                    <option value="warning" {{ request('level') === 'warning' ? 'selected' : '' }}>Warning</option>
                    <option value="info" {{ request('level') === 'info' ? 'selected' : '' }}>Info</option>
                    <option value="debug" {{ request('level') === 'debug' ? 'selected' : '' }}>Debug</option>
                </select>
            </div>

            <!-- Date From -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">From Date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
            </div>

            <!-- Date To -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">To Date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
            </div>

            <!-- Filter Buttons -->
            <div class="md:col-span-1 flex gap-1.5">
                <button type="submit" class="flex-1 py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs transition shadow-sm flex items-center justify-center">
                    Filter
                </button>
                @if (request()->hasAny(['search', 'level', 'from_date', 'to_date']))
                    <a href="{{ route('logs.index') }}" class="py-2 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl text-xs transition flex items-center justify-center" title="Clear Filters">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-800">
                Log Entries
                <span class="ml-1 text-xs text-slate-500 font-normal">({{ $logs->total() }} rows found)</span>
            </h3>
            <span class="text-xs text-slate-400">Showing page {{ $logs->currentPage() }} of {{ $logs->lastPage() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-4">Level</th>
                        <th class="py-3 px-4">Message & Exception</th>
                        <th class="py-3 px-4">File & Line</th>
                        <th class="py-3 px-4">HTTP Request Context</th>
                        <th class="py-3 px-4">Created By</th>
                        <th class="py-3 px-4">IP Address</th>
                        <th class="py-3 px-4">Timestamp</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition group">
                            <!-- Level -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $log->badge_class }}">
                                    {{ $log->level }}
                                </span>
                            </td>

                            <!-- Message & Exception -->
                            <td class="py-3.5 px-4 max-w-sm">
                                <p class="font-semibold text-slate-900 truncate" title="{{ $log->message }}">
                                    {{ $log->message }}
                                </p>
                                @if ($log->exception)
                                    <p class="text-[11px] text-rose-600 font-mono truncate mt-0.5" title="{{ $log->exception }}">
                                        {{ class_basename($log->exception) }}
                                    </p>
                                @endif
                            </td>

                            <!-- File & Line -->
                            <td class="py-3.5 px-4 max-w-xs">
                                @if ($log->file)
                                    <div class="font-mono text-[11px] text-slate-600 truncate" title="{{ $log->file }}">
                                        {{ $log->short_file }}
                                    </div>
                                    @if ($log->line)
                                        <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded bg-slate-100 text-slate-500 font-mono text-[10px]">
                                            Line {{ $log->line }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <!-- HTTP Request Context (Method, Route, URL) -->
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if ($log->method)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ in_array(strtoupper($log->method), ['POST', 'PUT', 'DELETE']) ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $log->method }}
                                        </span>
                                    @endif
                                    @if ($log->route_name)
                                        <span class="font-mono text-[11px] text-indigo-600 font-medium truncate" title="Route: {{ $log->route_name }}">
                                            {{ $log->route_name }}
                                        </span>
                                    @endif
                                </div>
                                @if ($log->url)
                                    <p class="text-[10px] text-slate-400 truncate mt-1" title="{{ $log->url }}">
                                        {{ Str::limit($log->url, 40) }}
                                    </p>
                                @endif
                            </td>

                            <!-- Created By -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @php
                                    $creator = $log->creator;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px] font-bold uppercase shrink-0">
                                        {{ strtoupper(substr($log->user ?? 'G', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800 text-xs">
                                            {{ $log->user ?? 'Guest / System' }}
                                        </div>
                                        @if ($creator)
                                            <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wide
                                                {{ match($creator->role) {
                                                    'Super Admin' => 'bg-purple-100 text-purple-700',
                                                    'Admin'       => 'bg-indigo-100 text-indigo-700',
                                                    'Sales'       => 'bg-emerald-100 text-emerald-700',
                                                    'Accountant'  => 'bg-amber-100 text-amber-700',
                                                    default       => 'bg-slate-100 text-slate-600',
                                                } }}">
                                                {{ $creator->role }}
                                            </span>
                                        @else
                                            <span class="text-[10px] text-slate-400">{{ $log->company ?? 'Guest' }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- IP Address -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-mono text-[11px] text-slate-600 bg-slate-100 px-2 py-0.5 rounded">
                                    {{ $log->ip ?? '—' }}
                                </span>
                            </td>

                            <!-- Timestamp -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="text-slate-800 font-medium">
                                    {{ $log->created_at ? $log->created_at->format('M d, Y H:i:s') : '—' }}
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    {{ $log->created_at ? $log->created_at->diffForHumans() : '' }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- View Details Modal Trigger -->
                                    <button type="button" 
                                            onclick='openLogModal(@json($log))' 
                                            class="p-1.5 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 transition" 
                                            title="View Details & Stack Trace">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    <!-- Delete Single Row Button -->
                                    <form action="{{ route('logs.destroy', $log) }}" method="POST" class="inline" onsubmit="return confirmDeleteLog(event, {{ $log->id }})">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete Log Entry">
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
                            <td colspan="8" class="py-12 px-4 text-center">
                                <div class="max-w-sm mx-auto text-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">Clean State: No Logs Recorded</h4>
                                    <p class="text-xs text-slate-500 mt-1">No system errors or captured events match your current filter criteria.</p>
                                    <div class="mt-4">
                                        <a href="{{ route('logs.test', ['type' => 'exception']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                            Generate Test Log
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if ($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Log Details & Stack Trace Modal -->
<div id="logDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" onclick="closeLogModal()"></div>

        <!-- Center trick -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Dialog Box -->
        <div class="inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl border border-slate-200 sm:align-middle">
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <span id="modalLevelBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider"></span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900" id="modalExceptionClass">Log Details</h3>
                        <p class="text-xs text-slate-500" id="modalTimestamp"></p>
                    </div>
                </div>
                <button type="button" onclick="closeLogModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-5 space-y-5 max-h-[70vh] overflow-y-auto pr-1">
                <!-- Message Box -->
                <div>
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Message</h4>
                    <div id="modalMessage" class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-sm font-medium text-slate-800 break-words font-mono"></div>
                </div>

                <!-- 12 Fields Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block mb-0.5 font-semibold">Source File & Line</span>
                        <span id="modalFile" class="font-mono text-slate-800 break-all"></span>
                    </div>

                    <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block mb-0.5 font-semibold">Route & Method</span>
                        <div class="flex items-center gap-2">
                            <span id="modalMethod" class="font-bold text-slate-700"></span>
                            <span id="modalRoute" class="font-mono text-indigo-600"></span>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block mb-0.5 font-semibold">Full URL</span>
                        <span id="modalUrl" class="font-mono text-slate-700 break-all"></span>
                    </div>

                    <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block mb-0.5 font-semibold">Client IP Address</span>
                        <span id="modalIp" class="font-mono text-slate-800"></span>
                    </div>

                    <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block mb-0.5 font-semibold">Created By</span>
                        <span id="modalUser" class="font-medium text-slate-800"></span>
                        <span id="modalCreatedByRole" class="ml-1 text-[10px] text-slate-400"></span>
                    </div>

                    <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block mb-0.5 font-semibold">Company Context</span>
                        <span id="modalCompany" class="font-medium text-slate-800"></span>
                    </div>
                </div>

                <!-- Stack Trace Container -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                            <span>Stack Trace</span>
                        </h4>
                        <button type="button" onclick="copyStackTrace()" class="inline-flex items-center gap-1.5 text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                            </svg>
                            <span id="copyTraceBtnText">Copy Trace</span>
                        </button>
                    </div>
                    <div class="relative bg-slate-900 rounded-xl p-4 overflow-x-auto border border-slate-800 max-h-80">
                        <pre id="modalStackTrace" class="font-mono text-[11px] text-slate-300 whitespace-pre leading-relaxed select-all"></pre>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="mt-6 pt-4 border-t border-slate-200 flex justify-end">
                <button type="button" onclick="closeLogModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Close test dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('testDropdownWrapper');
        const dropdown = document.getElementById('testDropdown');
        if (wrapper && !wrapper.contains(e.target) && dropdown) {
            dropdown.classList.add('hidden');
        }
    });

    // Modal display logic
    function openLogModal(log) {
        document.getElementById('modalLevelBadge').innerText = (log.level || 'ERROR').toUpperCase();
        document.getElementById('modalLevelBadge').className = 'px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider border ' + (log.badge_class || 'bg-rose-100 text-rose-800 border-rose-200');
        document.getElementById('modalExceptionClass').innerText = log.exception ? log.exception : 'System Log #' + log.id;
        document.getElementById('modalTimestamp').innerText = log.created_at ? log.created_at : '';
        document.getElementById('modalMessage').innerText = log.message || '—';
        document.getElementById('modalFile').innerText = (log.file ? log.file : '—') + (log.line ? ' (Line ' + log.line + ')' : '');
        document.getElementById('modalMethod').innerText = log.method ? log.method.toUpperCase() : '—';
        document.getElementById('modalRoute').innerText = log.route_name ? log.route_name : '—';
        document.getElementById('modalUrl').innerText = log.url || '—';
        document.getElementById('modalIp').innerText = log.ip || '—';
        document.getElementById('modalUser').innerText = log.user || 'Guest / System';
        const creatorRole = log.creator ? log.creator.role : '';
        document.getElementById('modalCreatedByRole').innerText = creatorRole ? '(' + creatorRole + ')' : '';
        document.getElementById('modalCompany').innerText = log.company || '—';
        document.getElementById('modalStackTrace').innerText = log.stack_trace || 'No stack trace captured for this event.';

        document.getElementById('logDetailModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeLogModal() {
        document.getElementById('logDetailModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    // Copy Stack Trace to clipboard
    function copyStackTrace() {
        const text = document.getElementById('modalStackTrace').innerText;
        navigator.clipboard.writeText(text).then(function() {
            const btn = document.getElementById('copyTraceBtnText');
            btn.innerText = 'Copied!';
            setTimeout(() => { btn.innerText = 'Copy Trace'; }, 2000);
        });
    }

    // SweetAlert2 confirm for single log deletion
    function confirmDeleteLog(event, id) {
        event.preventDefault();
        const form = event.target;
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete this log entry?',
                text: 'Log #' + id + ' will be permanently removed.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }
        if (confirm('Delete this log entry permanently?')) {
            form.submit();
        }
        return false;
    }

    // SweetAlert2 confirm for clearing all logs
    function confirmClearLogs() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Clear ALL system logs?',
                text: 'This action will permanently wipe all logs and stack traces from the database. This cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, clear all',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('clearLogsForm').submit();
                }
            });
            return;
        }
        if (confirm('Are you sure you want to clear ALL system logs? This cannot be undone.')) {
            document.getElementById('clearLogsForm').submit();
        }
    }
</script>
@endsection
