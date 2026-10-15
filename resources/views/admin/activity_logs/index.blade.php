@extends('layout.app')

@section('page_title', 'Activity Logs')
@section('page_description', 'Audit trail of transactions, user operations and data modifications')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Breadcrumb Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-1">
                <span class="inline-flex items-center gap-1 text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Audit & Security
                </span>
                <span>/</span>
                <span class="text-indigo-600 font-semibold">Activity Logs</span>
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    Live Transaction Tracker
                </span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Business Transactions & Audit Trail</h1>
            <p class="text-xs text-slate-500 mt-0.5">Captures every booking, invoice, receipt, container and master-data creation, edit and deletion.</p>
        </div>

        <div class="flex items-center flex-wrap gap-2.5">
            <!-- Clear Activity Logs Button (Super Admin Only) -->
            @if (auth()->user()?->isSuperAdmin() && $metrics['total'] > 0)
                <button type="button" onclick="confirmClearActivities()" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition shadow-sm">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Clear Activities</span>
                </button>

                <form id="clearActivitiesForm" action="{{ route('activity_logs.clear') }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif

            <!-- Refresh Button -->
            <a href="{{ route('activity_logs.index') }}" class="p-2 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition shadow-sm" title="Refresh">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Transactions -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Transactions</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($metrics['total']) }}</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Logged model transactions</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
        </div>

        <!-- Created -->
        <div class="bg-white p-5 rounded-2xl border border-emerald-100 shadow-sm flex items-center justify-between bg-gradient-to-br from-white to-emerald-50/30">
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Created</p>
                <h3 class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($metrics['created']) }}</h3>
                <p class="text-[11px] text-emerald-500 mt-0.5">New records added</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
        </div>

        <!-- Updated -->
        <div class="bg-white p-5 rounded-2xl border border-amber-100 shadow-sm flex items-center justify-between bg-gradient-to-br from-white to-amber-50/30">
            <div>
                <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Updated / Modified</p>
                <h3 class="text-2xl font-black text-amber-700 mt-1">{{ number_format($metrics['updated']) }}</h3>
                <p class="text-[11px] text-amber-500 mt-0.5">Edits & status changes</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
        </div>

        <!-- Deleted -->
        <div class="bg-white p-5 rounded-2xl border border-rose-100 shadow-sm flex items-center justify-between bg-gradient-to-br from-white to-rose-50/30">
            <div>
                <p class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Deleted</p>
                <h3 class="text-2xl font-black text-rose-700 mt-1">{{ number_format($metrics['deleted']) }}</h3>
                <p class="text-[11px] text-rose-500 mt-0.5">Removed transactions</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('activity_logs.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end">
            <!-- Search Text -->
            <div class="md:col-span-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Search Transactions</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search description, attribute, or value..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Subject Type Dropdown -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Subject / Model</label>
                <select name="subject_type" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">All Models</option>
                    @foreach ($subjectTypes as $key => $label)
                        <option value="{{ $key }}" {{ request('subject_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Event / Action Dropdown -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Action</label>
                <select name="event" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">All Actions</option>
                    <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>

            <!-- Causer / Created By Dropdown -->
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Created By</label>
                <select name="causer_id" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">All Users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ (string)request('causer_id') === (string)$user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->role }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Submit Buttons -->
            <div class="md:col-span-2 flex gap-1.5">
                <button type="submit" class="flex-1 py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs transition shadow-sm flex items-center justify-center">
                    Filter
                </button>
                @if (request()->hasAny(['search', 'subject_type', 'event', 'causer_id', 'from_date', 'to_date']))
                    <a href="{{ route('activity_logs.index') }}" class="py-2 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl text-xs transition flex items-center justify-center" title="Clear Filters">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Activities Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-800">
                Transaction Audit Trail
                <span class="ml-1 text-xs text-slate-500 font-normal">({{ $activities->total() }} records found)</span>
            </h3>
            <span class="text-xs text-slate-400">Showing page {{ $activities->currentPage() }} of {{ $activities->lastPage() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-4">Action</th>
                        <th class="py-3 px-4">Subject</th>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4">Created By</th>
                        <th class="py-3 px-4">Modified Attributes</th>
                        <th class="py-3 px-4">Timestamp</th>
                        <th class="py-3 px-4 text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($activities as $activity)
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
                            $causer = $activity->creator ?? $activity->causer;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition group">
                            <!-- Action Badge -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide border {{ $eventBadge }}">
                                    {{ $activity->event ?? 'ACTION' }}
                                </span>
                            </td>

                            <!-- Subject Model Badge -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $subjectClass }}
                                    </span>
                                    @if ($activity->subject_id)
                                        <span class="text-[11px] font-mono text-slate-500 font-semibold">
                                            #{{ $activity->subject_id }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Description -->
                            <td class="py-3.5 px-4 max-w-sm">
                                <p class="font-medium text-slate-800">
                                    {{ $activity->description }}
                                </p>
                            </td>

                            <!-- Created By -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @php
                                    $displayName = $activity->causer_name ?: ($causer?->name ?? 'System Process');
                                    $displayEmail = $activity->causer_email ?: ($causer?->email ?? null);
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px] font-bold uppercase shrink-0">
                                        {{ strtoupper(substr($displayName, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800 text-xs">
                                            {{ $displayName }}
                                        </div>
                                        @if ($causer)
                                            <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded-full text-[9px] font-bold uppercase tracking-wide
                                                {{ match($causer->role) {
                                                    'Super Admin' => 'bg-purple-100 text-purple-700',
                                                    'Admin'       => 'bg-indigo-100 text-indigo-700',
                                                    'Sales'       => 'bg-emerald-100 text-emerald-700',
                                                    'Accountant'  => 'bg-amber-100 text-amber-700',
                                                    default       => 'bg-slate-100 text-slate-600',
                                                } }}">
                                                {{ $causer->role }}
                                            </span>
                                        @elseif ($displayEmail)
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $displayEmail }}</span>
                                        @else
                                            <span class="text-[10px] text-slate-400">Automated</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Modified Attributes Summary -->
                            <td class="py-3.5 px-4 max-w-xs">
                                @if (!empty($attributes))
                                    <div class="flex items-center gap-1 flex-wrap">
                                        @foreach (array_slice(array_keys($attributes), 0, 3) as $attr)
                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-[10px]">
                                                {{ $attr }}
                                            </span>
                                        @endforeach
                                        @if (count($attributes) > 3)
                                            <span class="text-[10px] text-slate-400 font-medium">
                                                +{{ count($attributes) - 3 }} more
                                            </span>
                                        @endif
                                    </div>
                                @elseif (!empty($activity->properties) && count($activity->properties) > 0)
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 font-mono text-[10px]">
                                        {{ count($activity->properties) }} properties
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <!-- Timestamp -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="text-slate-800 font-medium">
                                    {{ $activity->created_at ? $activity->created_at->format('M d, Y H:i:s') : '—' }}
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    {{ $activity->created_at ? $activity->created_at->diffForHumans() : '' }}
                                </div>
                            </td>

                            <!-- View Details Button -->
                            @php
                                $causerRole  = $causer ? ($causer->role ?? 'System') : 'System';
                                $causerEmail = $displayEmail ?? '—';
                                $createdAt   = $activity->created_at ? $activity->created_at->format('M d, Y H:i:s') : '—';
                                $modalData   = [
                                    'id'           => $activity->id,
                                    'event'        => $activity->event,
                                    'badge'        => $eventBadge,
                                    'description'  => $activity->description,
                                    'subject_type' => $subjectClass,
                                    'subject_id'   => $activity->subject_id,
                                    'causer_name'  => $displayName,
                                    'causer_role'  => $causerRole,
                                    'causer_email' => $causerEmail,
                                    'created_at'   => $createdAt,
                                    'attributes'   => $attributes,
                                    'old'          => $old,
                                    'properties'   => $activity->properties,
                                ];
                            @endphp
                            <td class="py-3.5 px-4 whitespace-nowrap text-right">
                                <button type="button"
                                        onclick='openActivityModal(@json($modalData))'
                                        class="p-1.5 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 transition"
                                        title="View Full Changes & Properties">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center">
                                <div class="max-w-sm mx-auto text-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">No Activity Logs Yet</h4>
                                    <p class="text-xs text-slate-500 mt-1">Transaction audit logs will be automatically recorded whenever users create, edit or delete records.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if ($activities->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Activity Detail & Diff Modal -->
<div id="activityDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-sm" onclick="closeActivityModal()"></div>

        <!-- Center trick -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Dialog Box -->
        <div class="inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl border border-slate-200 sm:align-middle">
            <!-- Modal Header -->
            <div class="flex items-start justify-between pb-4 border-b border-slate-200">
                <div class="flex items-center gap-3">
                    <span id="actModalEventBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider border"></span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900" id="actModalSubject">Transaction Details</h3>
                        <p class="text-xs text-slate-500" id="actModalTimestamp"></p>
                    </div>
                </div>
                <button type="button" onclick="closeActivityModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="mt-5 space-y-5 max-h-[70vh] overflow-y-auto pr-1">
                <!-- Description Box -->
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Description</span>
                    <p id="actModalDescription" class="text-sm font-semibold text-slate-800"></p>
                </div>

                <!-- Context Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block mb-0.5 font-semibold">Created By</span>
                        <span id="actModalCauser" class="font-bold text-slate-800 text-sm"></span>
                        <div class="flex items-center gap-2 mt-1">
                            <span id="actModalCauserRole" class="px-1.5 py-0.2 rounded-full text-[10px] font-bold uppercase bg-indigo-100 text-indigo-700"></span>
                            <span id="actModalCauserEmail" class="text-slate-500 font-mono text-[11px]"></span>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block mb-0.5 font-semibold">Affected Record</span>
                        <span id="actModalRecord" class="font-mono text-slate-800 text-sm font-bold"></span>
                    </div>
                </div>

                <!-- Field Changes Diff Table -->
                <div id="actModalChangesSection">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Attribute Changes</h4>
                    <div class="overflow-hidden rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-100/80 text-[10px] font-bold text-slate-600 uppercase tracking-wider border-b border-slate-200">
                                    <th class="py-2.5 px-3">Field</th>
                                    <th class="py-2.5 px-3">Old Value</th>
                                    <th class="py-2.5 px-3">New Value</th>
                                </tr>
                            </thead>
                            <tbody id="actModalChangesTableBody" class="divide-y divide-slate-100 font-mono text-[11px]">
                                <!-- Injected via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Raw JSON Properties (Collapsible) -->
                <div>
                    <button type="button"
                            onclick="document.getElementById('actModalRawJson').classList.toggle('hidden')"
                            class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1 transition">
                        <span>Toggle Raw Properties JSON</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="actModalRawJson" class="hidden mt-2 bg-slate-900 rounded-xl p-3.5 overflow-x-auto border border-slate-800">
                        <pre id="actModalProperties" class="font-mono text-[11px] text-slate-300 whitespace-pre leading-relaxed"></pre>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="mt-6 pt-4 border-t border-slate-200 flex justify-end">
                <button type="button" onclick="closeActivityModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openActivityModal(data) {
        document.getElementById('actModalEventBadge').innerText = (data.event || 'ACTION').toUpperCase();
        document.getElementById('actModalEventBadge').className = 'px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider border ' + (data.badge || 'bg-indigo-100 text-indigo-800 border-indigo-200');
        document.getElementById('actModalSubject').innerText = data.subject_type + ' #' + (data.subject_id || '—');
        document.getElementById('actModalTimestamp').innerText = data.created_at;
        document.getElementById('actModalDescription').innerText = data.description;
        document.getElementById('actModalCauser').innerText = data.causer_name;
        document.getElementById('actModalCauserRole').innerText = data.causer_role;
        document.getElementById('actModalCauserEmail').innerText = data.causer_email;
        document.getElementById('actModalRecord').innerText = data.subject_type + (data.subject_id ? ' ID: ' + data.subject_id : '');

        // Render changes table
        const tbody = document.getElementById('actModalChangesTableBody');
        tbody.innerHTML = '';
        const attributes = data.attributes || {};
        const old = data.old || {};
        const allKeys = Array.from(new Set([...Object.keys(attributes), ...Object.keys(old)]));

        if (allKeys.length > 0) {
            document.getElementById('actModalChangesSection').classList.remove('hidden');
            allKeys.forEach(key => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50 transition';

                const oldVal = old[key] !== undefined ? JSON.stringify(old[key]) : '<span class="text-slate-400 italic">None</span>';
                const newVal = attributes[key] !== undefined ? JSON.stringify(attributes[key]) : '<span class="text-slate-400 italic">None</span>';

                tr.innerHTML = `
                    <td class="py-2 px-3 font-semibold text-slate-700 bg-slate-50/50">${key}</td>
                    <td class="py-2 px-3 text-rose-700 bg-rose-50/20">${oldVal}</td>
                    <td class="py-2 px-3 text-emerald-700 bg-emerald-50/20">${newVal}</td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            document.getElementById('actModalChangesSection').classList.add('hidden');
        }

        // Raw properties
        document.getElementById('actModalProperties').innerText = JSON.stringify(data.properties, null, 2);

        document.getElementById('activityDetailModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeActivityModal() {
        document.getElementById('activityDetailModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function confirmClearActivities() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Clear ALL activity logs?',
                text: 'This action will permanently erase the transaction audit trail. This cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, clear all',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('clearActivitiesForm').submit();
                }
            });
            return;
        }
        if (confirm('Are you sure you want to clear ALL activity logs? This cannot be undone.')) {
            document.getElementById('clearActivitiesForm').submit();
        }
    }
</script>
@endsection
