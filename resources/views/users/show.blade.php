@extends('layout.app')

@section('page_title', 'User Profile: ' . $user->name)
@section('page_description', 'Detailed view of user account credentials, security status, and role access')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Top Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Users List
        </a>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Edit User</span>
            </a>

            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Permanently delete user {{ addslashes($user->name) }}?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    <span>Delete User</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Profile Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="h-28 bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-900 relative"></div>
        <div class="px-6 pb-6 relative">
            <!-- Avatar & Header info -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between -mt-12 gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-end gap-4">
                    <div class="w-20 h-20 rounded-2xl bg-white p-1 shadow-md">
                        <div class="w-full h-full rounded-xl bg-gradient-to-tr from-slate-800 to-indigo-600 text-white font-black text-2xl flex items-center justify-center">
                            {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900">{{ $user->name }}</h2>
                        <p class="text-xs text-slate-500 font-medium">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $user->role_badge_class }}">
                        {{ $user->role ?? 'Employee' }}
                    </span>
                    @if ($user->isSuperAdmin())
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            Super Admin Flag Active
                        </span>
                    @endif
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                <!-- Account Info -->
                <div class="p-4 bg-slate-50/80 rounded-xl border border-slate-100 space-y-3">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Account Credentials</h3>
                    
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">User ID</span>
                        <span class="font-mono font-semibold text-slate-800">#{{ $user->id }}</span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Email Address</span>
                        <span class="font-medium text-slate-800">{{ $user->email }}</span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Password Encryption</span>
                        <span class="font-mono text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">Bcrypt (Hashed)</span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Email Status</span>
                        @if ($user->email_verified_at)
                            <span class="text-emerald-700 font-semibold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Verified ({{ $user->email_verified_at->format('M d, Y') }})
                            </span>
                        @else
                            <span class="text-slate-400">Not verified</span>
                        @endif
                    </div>
                </div>

                <!-- Registration Timestamps -->
                <div class="p-4 bg-slate-50/80 rounded-xl border border-slate-100 space-y-3">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Account Timestamps</h3>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Registered On</span>
                        <span class="font-medium text-slate-800">{{ $user->created_at ? $user->created_at->format('M d, Y H:i:s') : '—' }}</span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Time Elapsed</span>
                        <span class="text-slate-600">{{ $user->created_at ? $user->created_at->diffForHumans() : '—' }}</span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500">Last Profile Update</span>
                        <span class="font-medium text-slate-800">{{ $user->updated_at ? $user->updated_at->format('M d, Y H:i:s') : '—' }}</span>
                    </div>
                </div>
            </div>

            <!-- Role Permissions & Scope Info -->
            <div class="mt-4 p-5 rounded-xl border border-slate-200 bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Role Permissions Summary</h3>
                <div class="text-xs text-slate-600 leading-relaxed">
                    @switch($user->role)
                        @case('Super Admin')
                            <p class="font-semibold text-purple-900 mb-1">Master Privileges Enabled</p>
                            <p>Full unrestricted access across the entire shipping management suite: System Logs inspection, database telemetry, user account provisioning, role adjustments, and security policies.</p>
                            @break
                        @case('Admin')
                            <p class="font-semibold text-indigo-900 mb-1">Administrative Privileges Enabled</p>
                            <p>Administrative management of partner companies, port terminals, container inventories, invoices, and operational reports.</p>
                            @break
                        @case('Sales')
                            <p class="font-semibold text-emerald-900 mb-1">Commercial Privileges Enabled</p>
                            <p>Client onboarding, portfolio management, quotation creation, sales pipeline visibility, and container shipment bookings.</p>
                            @break
                        @case('Accountant')
                            <p class="font-semibold text-amber-900 mb-1">Financial Privileges Enabled</p>
                            <p>Invoicing, receipt issuance, multiple payment method transactions, refund management, and financial PDF exports.</p>
                            @break
                        @default
                            <p class="font-semibold text-slate-800 mb-1">Standard Staff Privileges</p>
                            <p>Standard day-to-day warehouse operations, container inventory inspections, and port dispatch tracking.</p>
                    @endswitch
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
