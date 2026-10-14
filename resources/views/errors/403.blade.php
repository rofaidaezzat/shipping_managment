<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - Access Restricted</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-6 selection:bg-rose-500 selection:text-white">
    <div class="max-w-lg w-full text-center space-y-6">
        <!-- Shield Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-rose-500/10 border border-rose-500/20 text-rose-500 shadow-xl shadow-rose-500/5">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        <div class="space-y-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                HTTP 403 Forbidden
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">Super Admin Access Only</h1>
            <p class="text-sm text-slate-400 max-w-md mx-auto leading-relaxed">
                {{ $exception->getMessage() ?: 'Access Restricted. Only a Super Admin can open and view System Logs.' }}
            </p>
        </div>

        <!-- Role Permissions Context Card -->
        <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/80 text-left text-xs space-y-2 text-slate-300">
            <div class="flex items-center justify-between pb-2 border-b border-slate-700/50">
                <span class="text-slate-400">Required Role:</span>
                <span class="font-bold text-purple-400 uppercase tracking-wide">Super Admin</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-400">Current User:</span>
                <span class="font-medium text-slate-200">
                    {{ auth()->check() ? auth()->user()->name . ' (' . (auth()->user()->role ?? 'User') . ')' : 'Guest / Unauthenticated' }}
                </span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ route('companies.index') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition">
                Return to Dashboard
            </a>

            @if (app()->environment('local'))
                <a href="{{ route('logs.index', ['as_super_admin' => 1]) }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition">
                    Open as Super Admin
                </a>
            @endif
        </div>
    </div>
</body>
</html>
