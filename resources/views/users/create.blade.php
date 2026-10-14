@extends('layout.app')

@section('page_title', 'Create New User')
@section('page_description', 'Add a new system user, assign credentials, and set access role')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Users List
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">User Account Registration</h2>
                    <p class="text-xs text-slate-500">Provide the user's personal info, authentication credentials, and access role</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('users.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Personal Info (Name & Email) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Full Name <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </span>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}" 
                               required 
                               placeholder="e.g. John Doe" 
                               class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('name') ? 'border-rose-400 focus:ring-rose-500' : 'border-slate-300 focus:ring-indigo-500' }} focus:outline-none focus:ring-2">
                    </div>
                    @error('name')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Email Address <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </span>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}" 
                               required 
                               placeholder="e.g. user@example.com" 
                               class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-xl border {{ $errors->has('email') ? 'border-rose-400 focus:ring-rose-500' : 'border-slate-300 focus:ring-indigo-500' }} focus:outline-none focus:ring-2">
                    </div>
                    @error('email')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Passwords (Password & Confirm) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               minlength="6"
                               placeholder="Minimum 6 characters" 
                               class="w-full pl-9 pr-10 py-2.5 text-xs rounded-xl border {{ $errors->has('password') ? 'border-rose-400 focus:ring-rose-500' : 'border-slate-300 focus:ring-indigo-500' }} focus:outline-none focus:ring-2">
                        <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Confirm Password <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </span>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               required 
                               minlength="6"
                               placeholder="Re-enter password" 
                               class="w-full pl-9 pr-10 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <button type="button" onclick="togglePasswordVisibility('password_confirmation', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Role Selection -->
            <div class="pt-3">
                <label class="block text-xs font-semibold text-slate-700 mb-2">
                    Select Access Role <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <!-- Super Admin -->
                    <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition hover:bg-purple-50/40 {{ old('role') === 'Super Admin' ? 'border-purple-500 bg-purple-50/50 ring-2 ring-purple-500' : 'border-slate-200 bg-white' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                <svg class="w-3 h-3 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd"/>
                                </svg>
                                Super Admin
                            </span>
                            <input type="radio" name="role" value="Super Admin" {{ old('role') === 'Super Admin' ? 'checked' : '' }} required class="w-4 h-4 text-purple-600 focus:ring-purple-500">
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Full master access to all system features, System Logs, user administration, and security configurations.
                        </p>
                    </label>

                    <!-- Admin -->
                    <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition hover:bg-indigo-50/40 {{ old('role') === 'Admin' ? 'border-indigo-500 bg-indigo-50/50 ring-2 ring-indigo-500' : 'border-slate-200 bg-white' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                <svg class="w-3 h-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Admin
                            </span>
                            <input type="radio" name="role" value="Admin" {{ old('role') === 'Admin' ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Administrative control over companies, ports, containers, logistics tracking, and general operations.
                        </p>
                    </label>

                    <!-- Sales -->
                    <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition hover:bg-emerald-50/40 {{ old('role') === 'Sales' ? 'border-emerald-500 bg-emerald-50/50 ring-2 ring-emerald-500' : 'border-slate-200 bg-white' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                Sales
                            </span>
                            <input type="radio" name="role" value="Sales" {{ old('role') === 'Sales' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Manage client companies, sales bookings, quotation requests, and commercial container tracking.
                        </p>
                    </label>

                    <!-- Accountant -->
                    <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition hover:bg-amber-50/40 {{ old('role') === 'Accountant' ? 'border-amber-500 bg-amber-50/50 ring-2 ring-amber-500' : 'border-slate-200 bg-white' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Accountant
                            </span>
                            <input type="radio" name="role" value="Accountant" {{ old('role') === 'Accountant' ? 'checked' : '' }} class="w-4 h-4 text-amber-600 focus:ring-amber-500">
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Access invoices, payment receipts, payment methods, transaction reconciliations, and financial exports.
                        </p>
                    </label>

                    <!-- Employee -->
                    <label class="relative flex flex-col p-4 rounded-xl border cursor-pointer transition hover:bg-slate-50 {{ (old('role') === 'Employee' || !old('role')) ? 'border-indigo-500 bg-slate-50 ring-2 ring-indigo-500' : 'border-slate-200 bg-white' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Employee
                            </span>
                            <input type="radio" name="role" value="Employee" {{ (old('role') === 'Employee' || !old('role')) ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Standard employee access to view assignments, port logistics, and day-to-day warehouse operations.
                        </p>
                    </label>
                </div>
                @error('role')
                    <p class="text-rose-600 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="pt-5 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Create User</span>
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function togglePasswordVisibility(fieldId, btn) {
        const input = document.getElementById(fieldId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.classList.add('text-indigo-600');
            btn.classList.remove('text-slate-400');
        } else {
            input.type = 'password';
            btn.classList.remove('text-indigo-600');
            btn.classList.add('text-slate-400');
        }
    }
</script>
@endsection
