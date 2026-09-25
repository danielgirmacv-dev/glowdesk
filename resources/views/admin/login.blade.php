@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm">

        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl glow-gradient flex items-center justify-center mx-auto mb-4 shadow-sm">
                <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2s-4 2-4 8c0 4 4 12 4 12s4-8 4-12c0-6-4-8-4-8z"/>
                    <path d="M14 4s4 4 4 8c0 3-2 6-6 10"/>
                    <path d="M10 4s-4 4-4 8c0 3 2 6 6 10"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Admin Access</h1>
            <p class="text-slate-500 text-sm mt-1">Enter your password to continue</p>
        </div>

        <!-- Card -->
        <div class="glass-dark rounded-2xl p-8 border border-slate-200/80 dark:border-white/10 shadow-sm">

            @if(isset($errors) && $errors->any())
                <div class="mb-5 glass border border-red-500/30 text-red-400 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" name="password" required autofocus placeholder="••••••••"
                        class="input-field w-full rounded-xl py-3 px-4 text-sm">
                </div>

                <button type="submit" class="w-full btn-glow py-3 rounded-xl text-sm font-bold text-white flex items-center justify-center gap-2 mt-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Sign In to GlowAddis
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-600 mt-6">
            Access restricted to authorized administrators only
        </p>
    </div>
</div>
@endsection
