<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shipping Management System')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-200 flex flex-col shrink-0 sticky top-0 h-screen overflow-y-auto shadow-xl">
        <!-- Logo / Brand -->
        <div class="h-16 flex items-center px-6 bg-slate-950/70 border-b border-slate-800 gap-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-base font-bold tracking-tight text-white leading-tight">CargoPort</h1>
                <p class="text-xs text-slate-400">Shipping System</p>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-1">
            <div class="px-3 pb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">
                Management
            </div>

            <!-- Companies Link -->
            <a href="{{ route('companies.index') }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('companies.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>Companies</span>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ request()->routeIs('companies.*') ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-800 text-slate-400' }}">
                    {{ \App\Models\Company::count() }}
                </span>
            </a>

            <!-- Ports Link -->
            <a href="{{ route('ports.index') }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('ports.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Ports</span>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ request()->routeIs('ports.*') ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-800 text-slate-400' }}">
                    {{ \App\Models\Port::count() }}
                </span>
            </a>

            <!-- Containers Link -->
            <a href="{{ route('containers.index') }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('containers.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span>Containers</span>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ request()->routeIs('containers.*') ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-800 text-slate-400' }}">
                    {{ \App\Models\Container::count() }}
                </span>
            </a>

            <!-- Invoices Link -->
            <a href="{{ route('invoices.index') }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ (request()->routeIs('invoices.*') && !request()->routeIs('invoices.receipts.*')) ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Invoices</span>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ (request()->routeIs('invoices.*') && !request()->routeIs('invoices.receipts.*')) ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-800 text-slate-400' }}">
                    {{ \App\Models\Invoice::count() }}
                </span>
            </a>

            <!-- Receipts Link -->
            <a href="{{ route('receipts.index') }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ (request()->routeIs('receipts.*') || request()->routeIs('invoices.receipts.*')) ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8h4"/>
                    </svg>
                    <span>Receipts</span>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ (request()->routeIs('receipts.*') || request()->routeIs('invoices.receipts.*')) ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-800 text-slate-400' }}">
                    {{ \App\Models\Receipt::count() }}
                </span>
            </a>

            <!-- Users Link -->
            <a href="{{ route('users.index') }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Users</span>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ request()->routeIs('users.*') ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-800 text-slate-400' }}">
                    {{ \App\Models\User::count() }}
                </span>
            </a>

            <!-- System & Logs Section -->
            <div class="pt-4 pb-1">
                <p class="px-3 text-[10px] font-bold tracking-wider text-slate-500 uppercase">Audit & Security</p>
            </div>

            <!-- Activity Logs Link (Transactions & Audit Trail) -->
            <a href="{{ route('activity_logs.index') }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('activity_logs.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Activity Logs</span>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full {{ request()->routeIs('activity_logs.*') ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-800 text-slate-400' }}">
                    {{ \Spatie\Activitylog\Models\Activity::count() }}
                </span>
            </a>

            <!-- System Logs Link (Super Admin Only: Errors & Exceptions) -->
            <a href="{{ route('logs.index') }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium transition {{ request()->routeIs('logs.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>System Logs</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-[9px] uppercase font-bold tracking-wide px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">Super Admin</span>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ request()->routeIs('logs.*') ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-800 text-slate-400' }}">
                        {{ \App\Models\SystemLog::count() }}
                    </span>
                </div>
            </a>
        </nav>

        <!-- Current User Profile & System Status Footer -->
        @php
            $activeUser = auth()->user() ?? \App\Models\User::first();
        @endphp
        <div class="p-3.5 border-t border-slate-800 bg-slate-950/70 space-y-2">
            <div class="flex items-center justify-between gap-2 p-1.5 rounded-xl bg-slate-900/60 border border-slate-800">
                <a href="{{ route('users.index') }}" class="flex items-center gap-2.5 min-w-0 flex-1 hover:opacity-90 transition group" title="Manage Users">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm">
                        {{ $activeUser ? strtoupper(substr($activeUser->name, 0, 1)) . strtoupper(substr(explode(' ', $activeUser->name)[1] ?? '', 0, 1)) : 'SA' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-200 truncate group-hover:text-indigo-300 transition">{{ $activeUser?->name ?? 'Super Admin' }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ $activeUser?->role ?? 'Super Admin' }}</p>
                    </div>
                </a>

                <!-- Sign Out Form -->
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition" title="Sign Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>

            <div class="flex items-center justify-between text-[11px] text-slate-500 px-1 pt-1">
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-slate-400 font-medium">System Online</span>
                </div>
                <span>MySQL</span>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Topbar Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">@yield('page_title', 'Dashboard')</h2>
                <p class="text-xs text-slate-500">@yield('page_description', 'Shipping logistics & inventory')</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden md:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Strict Deletion Protection Active
                </span>

                <!-- Active User Chip in Header -->
                <a href="{{ route('users.index') }}" class="flex items-center gap-2.5 pl-3 pr-2 py-1.5 rounded-xl border border-slate-200 hover:border-indigo-300 bg-slate-50 hover:bg-indigo-50/40 transition shadow-xs group" title="View Users & Access">
                    <div class="text-right hidden sm:block">
                        <div class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 transition">{{ $activeUser?->name ?? 'Admin User' }}</div>
                        <div class="text-[10px] font-semibold text-indigo-600 uppercase tracking-wide">{{ $activeUser?->role ?? 'Super Admin' }}</div>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-purple-600 text-white font-bold text-xs flex items-center justify-center shadow-xs">
                        {{ $activeUser ? strtoupper(substr($activeUser->name, 0, 1)) : 'U' }}
                    </div>
                </a>

                <!-- Header Sign Out Button -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="p-2 rounded-xl border border-slate-200 text-slate-500 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition shadow-xs" title="Sign Out">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- Body Container -->
        <main class="flex-1 p-8 overflow-y-auto">
            <!-- Flash Success Message -->
            @if (session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-sm">Success</h4>
                        <p class="text-sm mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Flash Error Message (e.g. Delete RESTRICT prevention) -->
            @if (session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-rose-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-sm">Action Prohibited (Constraint Protection)</h4>
                        <p class="text-sm mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Validation Errors -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-amber-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-sm">Please check the following errors:</h4>
                        <ul class="list-disc list-inside text-sm mt-1 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            @yield('content')
        </main>
    </div>

    <!-- SweetAlert2 & Alert Package -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('sweetalert::alert')

    <script>
        /**
         * Global SweetAlert confirmation dialog for rejecting receipts.
         */
        function confirmRejectReceipt(event, form, receiptNum, invoiceInfo) {
            if (event) event.preventDefault();
            Swal.fire({
                title: 'Reject ' + (receiptNum || 'Receipt') + '?',
                html: `
                    <div style="text-align: left; font-size: 13.5px; color: #475569; margin-top: 6px; line-height: 1.5;">
                        <p>Are you sure you want to reject this receipt?</p>
                        ${invoiceInfo ? `<div style="margin-top: 10px; padding: 10px 12px; border-radius: 8px; background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; font-size: 12px;">⚠️ Linked invoice(s): <strong>${invoiceInfo}</strong> will be reverted to <strong>UNPAID</strong> status.</div>` : ''}
                        <p style="margin-top: 10px; font-size: 11.5px; color: #94a3b8;">This receipt will not be deleted; it will be preserved in audit records as <strong>Rejected</strong>.</p>
                    </div>
                `,
                icon: 'warning',
                iconColor: '#f43f5e',
                showCancelButton: true,
                confirmButtonText: 'Yes, Reject Receipt',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                reverseButtons: true,
                focusCancel: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }
    </script>
</body>
</html>
