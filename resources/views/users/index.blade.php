@extends('layout.app')

@section('page_title', 'Users Management')
@section('page_description', 'Manage user accounts, credentials, and role-based access permissions')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-1">
                <span class="inline-flex items-center gap-1 text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    System & Access
                </span>
                <span>/</span>
                <span class="text-indigo-600 font-semibold">Users</span>
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">System Users & Access Roles</h1>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>Add New User</span>
            </a>
        </div>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- Total Users -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Users</p>
            <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($metrics['total']) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">All registered</p>
        </div>

        <!-- Super Admin -->
        <div class="bg-white p-4 rounded-2xl border border-purple-100 shadow-sm bg-gradient-to-br from-white to-purple-50/30">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-purple-700 uppercase tracking-wider">Super Admin</p>
                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
            </div>
            <h3 class="text-2xl font-black text-purple-800 mt-1">{{ number_format($metrics['super_admins']) }}</h3>
            <p class="text-[10px] text-purple-600 mt-0.5">Master control</p>
        </div>

        <!-- Admin -->
        <div class="bg-white p-4 rounded-2xl border border-indigo-100 shadow-sm bg-gradient-to-br from-white to-indigo-50/30">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-indigo-700 uppercase tracking-wider">Admin</p>
                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
            </div>
            <h3 class="text-2xl font-black text-indigo-800 mt-1">{{ number_format($metrics['admins']) }}</h3>
            <p class="text-[10px] text-indigo-600 mt-0.5">Management</p>
        </div>

        <!-- Sales -->
        <div class="bg-white p-4 rounded-2xl border border-emerald-100 shadow-sm bg-gradient-to-br from-white to-emerald-50/30">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Sales</p>
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
            </div>
            <h3 class="text-2xl font-black text-emerald-800 mt-1">{{ number_format($metrics['sales']) }}</h3>
            <p class="text-[10px] text-emerald-600 mt-0.5">Clients & Quotes</p>
        </div>

        <!-- Accountant -->
        <div class="bg-white p-4 rounded-2xl border border-amber-100 shadow-sm bg-gradient-to-br from-white to-amber-50/30">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Accountant</p>
                <span class="w-2 h-2 rounded-full bg-amber-600"></span>
            </div>
            <h3 class="text-2xl font-black text-amber-800 mt-1">{{ number_format($metrics['accountants']) }}</h3>
            <p class="text-[10px] text-amber-600 mt-0.5">Receipts & Invoices</p>
        </div>

        <!-- Employee -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold text-slate-600 uppercase tracking-wider">Employee</p>
                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
            </div>
            <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($metrics['employees']) }}</h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Operations staff</p>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3.5 items-end">
            <!-- Search Text -->
            <div class="md:col-span-7">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Search Users</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search by full name, email address, or role..." 
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <!-- Role Filter -->
            <div class="md:col-span-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Filter by Role</label>
                <select name="role" class="w-full py-2 px-3 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                    <option value="">All Roles</option>
                    @foreach (\App\Models\User::ROLES as $roleOption)
                        <option value="{{ $roleOption }}" {{ request('role') === $roleOption ? 'selected' : '' }}>
                            {{ $roleOption }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="md:col-span-2 flex gap-1.5">
                <button type="submit" class="flex-1 py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs transition shadow-sm flex items-center justify-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
                @if (request()->hasAny(['search', 'role']))
                    <a href="{{ route('users.index') }}" class="py-2 px-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl text-xs transition flex items-center justify-center" title="Reset Filters">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-800">
                User Accounts
                <span class="ml-1 text-xs text-slate-500 font-normal">({{ $users->total() }} total)</span>
            </h3>
            <span class="text-xs text-slate-400">Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-5">User</th>
                        <th class="py-3 px-5">Role</th>
                        <th class="py-3 px-5">Access Level</th>
                        <th class="py-3 px-5">Email Verified</th>
                        <th class="py-3 px-5">Registered Date</th>
                        <th class="py-3 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse ($users as $user)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- User Avatar & Email -->
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-slate-700 to-indigo-600 text-white font-bold text-sm flex items-center justify-center shadow-sm shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $user->name)[1] ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('users.show', $user) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition">
                                            {{ $user->name }}
                                        </a>
                                        <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                            </svg>
                                            <span>{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td class="py-4 px-5 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $user->role_badge_class }}">
                                    @if ($user->role === 'Super Admin')
                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
                                        </svg>
                                    @elseif ($user->role === 'Admin')
                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    @elseif ($user->role === 'Sales')
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                    @elseif ($user->role === 'Accountant')
                                        <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    @endif
                                    <span>{{ $user->role ?? 'Employee' }}</span>
                                </span>
                            </td>

                            <!-- Access Level (Super Admin Status) -->
                            <td class="py-4 px-5 whitespace-nowrap">
                                @if ($user->isSuperAdmin())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Full Super Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-slate-50 text-slate-600 border border-slate-200">
                                        Standard Role
                                    </span>
                                @endif
                            </td>

                            <!-- Email Verified -->
                            <td class="py-4 px-5 whitespace-nowrap">
                                @if ($user->email_verified_at)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Verified
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400">Unverified</span>
                                @endif
                            </td>

                            <!-- Registered Date -->
                            <td class="py-4 px-5 whitespace-nowrap">
                                <div class="text-slate-800 font-medium">
                                    {{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    {{ $user->created_at ? $user->created_at->diffForHumans() : '' }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-5 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- View Details -->
                                    <a href="{{ route('users.show', $user) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition" title="View Profile">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('users.edit', $user) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit User">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirmDeleteUser(event, '{{ addslashes($user->name) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Delete User">
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
                            <td colspan="6" class="py-12 px-4 text-center">
                                <div class="max-w-sm mx-auto text-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">No Users Found</h4>
                                    <p class="text-xs text-slate-500 mt-1">Try adjusting your search criteria or register a new user.</p>
                                    <div class="mt-4">
                                        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Create First User
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

<script>
    function confirmDeleteUser(event, userName) {
        event.preventDefault();
        const form = event.target;
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Delete user ' + userName + '?',
                text: 'This account will be permanently deleted and access revoked immediately.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete user',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }
        if (confirm('Are you sure you want to delete user ' + userName + '?')) {
            form.submit();
        }
        return false;
    }
</script>
@endsection
