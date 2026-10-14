<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Shipping Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-x-hidden selection:bg-indigo-500 selection:text-white">

    <!-- Ambient Gradient Blobs -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full relative z-10 space-y-6">

        <!-- Logo & Brand Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white shadow-xl shadow-indigo-500/20 mb-1">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Shipping Logistics</h1>
            <p class="text-xs text-slate-400">Enterprise Container & Freight Management</p>
        </div>

        <!-- Flash Success Notification -->
        @if (session('success'))
            <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center gap-2.5">
                <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Card Container -->
        <div class="bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl p-7 shadow-2xl shadow-black/60">
            <div class="mb-6">
                <h2 class="text-base font-bold text-white">Sign In to Your Account</h2>
                <p class="text-xs text-slate-400 mt-0.5">Enter your verified email and password to access the portal</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </span>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', 'admin@shipping.com') }}" 
                               required 
                               autofocus
                               placeholder="name@company.com" 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-950/70 border {{ $errors->has('email') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-800 focus:ring-indigo-500 focus:border-indigo-500' }} rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 transition">
                    </div>
                    @error('email')
                        <p class="text-rose-400 text-[11px] mt-1.5 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ $message }}</span>
                        </p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-300">Password</label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               value="password123"
                               required 
                               placeholder="••••••••" 
                               class="w-full pl-10 pr-10 py-2.5 bg-slate-950/70 border {{ $errors->has('password') ? 'border-rose-500 focus:ring-rose-500' : 'border-slate-800 focus:ring-indigo-500 focus:border-indigo-500' }} rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 transition">
                        <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-rose-400 text-[11px] mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" checked class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-slate-900">
                        <span class="text-xs text-slate-400">Remember this device</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-semibold text-xs shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 transition flex items-center justify-center gap-2">
                        <span>Sign In</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-6 pt-5 border-t border-slate-800">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 text-center">
                    One-Click Demo Credentials
                </p>
                <div class="grid grid-cols-1 gap-1.5">
                    <button type="button"
                            onclick="fillCredentials('superadmin@shipping.com', 'password123')"
                            class="flex items-center gap-3 p-2.5 rounded-xl border border-purple-500/30 bg-purple-500/10 hover:bg-purple-500/20 text-left transition">
                        <span class="w-2 h-2 rounded-full bg-purple-400 shrink-0"></span>
                        <div class="flex-1 min-w-0">
                            <span class="text-[11px] font-bold text-purple-300">Super Admin</span>
                            <span class="text-[10px] text-slate-400 font-mono ml-2">superadmin@shipping.com</span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-mono shrink-0">Full Access + System Logs</span>
                    </button>

                    <button type="button"
                            onclick="fillCredentials('admin@shipping.com', 'password123')"
                            class="flex items-center gap-3 p-2.5 rounded-xl border border-indigo-500/30 bg-indigo-500/10 hover:bg-indigo-500/20 text-left transition">
                        <span class="w-2 h-2 rounded-full bg-indigo-400 shrink-0"></span>
                        <div class="flex-1 min-w-0">
                            <span class="text-[11px] font-bold text-indigo-300">Admin</span>
                            <span class="text-[10px] text-slate-400 font-mono ml-2">admin@shipping.com</span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-mono shrink-0">Management Access</span>
                    </button>

                    <button type="button"
                            onclick="fillCredentials('sales@shipping.com', 'password123')"
                            class="flex items-center gap-3 p-2.5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 hover:bg-emerald-500/20 text-left transition">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                        <div class="flex-1 min-w-0">
                            <span class="text-[11px] font-bold text-emerald-300">Sales</span>
                            <span class="text-[10px] text-slate-400 font-mono ml-2">sales@shipping.com</span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-mono shrink-0">Commercial Access</span>
                    </button>

                    <button type="button"
                            onclick="fillCredentials('accountant@shipping.com', 'password123')"
                            class="flex items-center gap-3 p-2.5 rounded-xl border border-amber-500/30 bg-amber-500/10 hover:bg-amber-500/20 text-left transition">
                        <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                        <div class="flex-1 min-w-0">
                            <span class="text-[11px] font-bold text-amber-300">Accountant</span>
                            <span class="text-[10px] text-slate-400 font-mono ml-2">accountant@shipping.com</span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-mono shrink-0">Finance Access</span>
                    </button>

                    <button type="button"
                            onclick="fillCredentials('employee@shipping.com', 'password123')"
                            class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-700 bg-slate-800/40 hover:bg-slate-800 text-left transition">
                        <span class="w-2 h-2 rounded-full bg-slate-400 shrink-0"></span>
                        <div class="flex-1 min-w-0">
                            <span class="text-[11px] font-bold text-slate-300">Employee</span>
                            <span class="text-[10px] text-slate-400 font-mono ml-2">employee@shipping.com</span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-mono shrink-0">Standard Access</span>
                    </button>
                </div>
                <p class="text-[10px] text-slate-500 text-center mt-2.5">All passwords: <span class="font-mono text-slate-400">password123</span></p>
            </div>
        </div>

        <!-- Footer status -->
        <div class="text-center text-[11px] text-slate-500 flex items-center justify-center gap-3">
            <span class="flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                System Operational
            </span>
            <span>•</span>
            <span>SSL / TLS Encrypted</span>
        </div>

    </div>

    <script>
        function togglePasswordVisibility(id, btn) {
            const input = document.getElementById(id);
            if (input.type === 'password') {
                input.type = 'text';
                btn.classList.add('text-indigo-400');
            } else {
                input.type = 'password';
                btn.classList.remove('text-indigo-400');
            }
        }

        function fillCredentials(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
    </script>
</body>
</html>
