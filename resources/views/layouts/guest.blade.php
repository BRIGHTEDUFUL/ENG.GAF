<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ghana Air Force Engineering Command System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .radar-ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(14,165,233,0.3);
            animation: radar-expand 3s ease-out infinite;
        }
        @keyframes radar-expand {
            0%   { transform: scale(0.5); opacity: 0.8; }
            100% { transform: scale(2.5); opacity: 0; }
        }
        .aircraft-icon {
            filter: drop-shadow(0 0 12px rgba(14,165,233,0.6));
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%,100% { transform: translateY(0) rotate(-10deg); }
            50% { transform: translateY(-12px) rotate(-10deg); }
        }
        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            border-radius: 50%;
            background: rgba(14,165,233,0.6);
            animation: particle-float var(--dur, 8s) ease-in-out infinite var(--delay, 0s);
        }
        @keyframes particle-float {
            0%,100% { transform: translateY(0) translateX(0); opacity: 0.6; }
            33% { transform: translateY(-30px) translateX(15px); opacity: 1; }
            66% { transform: translateY(-15px) translateX(-10px); opacity: 0.4; }
        }
        .login-card { animation: slideInUp 0.6s cubic-bezier(0.22, 1, 0.36, 1); }
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .stat-pill {
            animation: statFadeIn 0.5s ease-out both;
        }
        @keyframes statFadeIn {
            from { opacity: 0; transform: scale(0.9) translateY(8px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }
        .stat-pill:nth-child(1) { animation-delay: 0.1s; }
        .stat-pill:nth-child(2) { animation-delay: 0.2s; }
        .stat-pill:nth-child(3) { animation-delay: 0.3s; }
        .stat-pill:nth-child(4) { animation-delay: 0.4s; }
        .input-group input:focus + label, .input-group input:not(:placeholder-shown) + label {
            transform: translateY(-22px) scale(0.8);
            color: #0EA5E9;
        }
        .grid-bg {
            background-image: linear-gradient(rgba(14,165,233,0.06) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(14,165,233,0.06) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</head>
<body class="font-sans antialiased overflow-hidden h-screen">
<div class="min-h-screen flex">

    {{-- ═══════ LEFT PANEL ═══════ --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gaf-gradient flex-col items-center justify-center">
        {{-- Animated grid --}}
        <div class="absolute inset-0 grid-bg opacity-40"></div>

        {{-- Glowing orbs --}}
        <div class="absolute top-20 left-20 w-64 h-64 rounded-full bg-sky-400/20 blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 rounded-full bg-blue-600/20 blur-3xl animate-pulse-slow" style="animation-delay:1.5s"></div>

        {{-- Particles --}}
        @for($i=0; $i<12; $i++)
        <div class="particle" style="--dur:{{ rand(6,12) }}s; --delay:{{ rand(0,5) }}s; top:{{ rand(5,90) }}%; left:{{ rand(5,90) }}%;"></div>
        @endfor

        {{-- Radar rings --}}
        <div class="absolute bottom-32 left-20 w-24 h-24 flex items-center justify-center">
            <div class="radar-ring w-24 h-24" style="animation-delay:0s"></div>
            <div class="radar-ring w-24 h-24" style="animation-delay:1s"></div>
            <div class="radar-ring w-24 h-24" style="animation-delay:2s"></div>
        </div>

        {{-- Content --}}
        <div class="relative z-10 text-center px-12 max-w-lg">

            {{-- Logo --}}
            <div class="flex items-center justify-center mb-8">
                <div class="relative">
                    <div class="w-36 h-36 rounded-full bg-white flex items-center justify-center shadow-gaf-glow overflow-hidden p-3 ring-4 ring-white/20">
                        <img src="{{ asset('images/gaf-logo.png') }}" alt="GAF Logo" class="w-full h-full object-contain">
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <span class="inline-block bg-white/10 backdrop-blur-sm text-sky-200 text-xs font-semibold px-3 py-1 rounded-full border border-white/20 tracking-widest uppercase mb-4">
                    🇬🇭 Republic of Ghana
                </span>
            </div>

            <h1 class="text-4xl font-black text-white leading-tight mb-2">
                Ghana Air Force
            </h1>
            <h2 class="text-xl font-semibold text-sky-300 mb-4">
                Engineering Command System
            </h2>
            <p class="text-sky-200/80 text-sm leading-relaxed max-w-xs mx-auto">
                Military-grade aircraft maintenance, engineering, and operational management platform.
            </p>

            {{-- Live Stats --}}
            <div class="mt-10 grid grid-cols-2 gap-3">
                <div class="stat-pill bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-4 text-center">
                    <p class="text-3xl font-black text-white">{{ \App\Models\Aircraft::count() }}</p>
                    <p class="text-sky-300 text-xs mt-1 font-medium">Aircraft</p>
                </div>
                <div class="stat-pill bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-4 text-center">
                    <p class="text-3xl font-black text-white">{{ \App\Models\MaintenanceTask::where('status','!=','completed')->count() }}</p>
                    <p class="text-sky-300 text-xs mt-1 font-medium">Open Tasks</p>
                </div>
                <div class="stat-pill bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-4 text-center">
                    <p class="text-3xl font-black text-white">{{ \App\Models\Incident::where('status','open')->count() }}</p>
                    <p class="text-sky-300 text-xs mt-1 font-medium">Active Incidents</p>
                </div>
                <div class="stat-pill bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-4 text-center">
                    <p class="text-3xl font-black text-white">{{ \App\Models\Wing::where('status','active')->count() }}</p>
                    <p class="text-sky-300 text-xs mt-1 font-medium">Active Wings</p>
                </div>
            </div>

            <p class="mt-10 text-sky-400/60 text-xs tracking-[0.3em] uppercase">
                🔒 Classified — Authorized Personnel Only
            </p>
        </div>
    </div>

    {{-- ═══════ RIGHT PANEL ═══════ --}}
    <div class="flex flex-1 flex-col items-center justify-center px-6 py-12 bg-white relative overflow-hidden">
        {{-- subtle bg pattern --}}
        <div class="absolute inset-0 grid-bg opacity-30"></div>
        <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-sky-100/60 blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full bg-blue-100/60 blur-3xl translate-y-1/2 -translate-x-1/2"></div>

        <div class="relative z-10 w-full max-w-sm login-card">
            {{-- Mobile logo --}}
            <div class="lg:hidden text-center mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 p-2 rounded-full bg-white shadow-md mb-3 border border-sky-50">
                    <img src="{{ asset('images/gaf-logo.png') }}" alt="GAF Logo" class="w-full h-full object-contain">
                </div>
                <h2 class="text-xl font-black text-gaf-navy">Ghana Air Force</h2>
                <p class="text-sky-600 text-sm">Engineering Command System</p>
            </div>

            {{-- Login card --}}
            <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-gaf-lg border border-sky-100 p-8">
                <div class="mb-7">
                    <h3 class="text-2xl font-black text-gaf-navy">Welcome Back</h3>
                    <p class="text-sky-600 text-sm mt-1">Sign in to access the command platform</p>
                </div>

                @if (session('status'))
                <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ session('status') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="mb-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    {{ $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 mb-1.5 uppercase tracking-wide">Email Address</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sky-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                   class="gaf-input pl-11" placeholder="admin@airforce.mil"
                                   required autofocus autocomplete="username"/>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 mb-1.5 uppercase tracking-wide">Password</label>
                        <div class="relative" x-data="{ show: false }">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sky-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input id="password" :type="show ? 'text' : 'password'" name="password"
                                   class="gaf-input pl-11 pr-12" placeholder="••••••••"
                                   required autocomplete="current-password"/>
                            <button type="button" @click="show=!show"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-sky-400 hover:text-gaf-blue transition-colors">
                                <svg x-show="!show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-sky-300 text-gaf-blue focus:ring-sky-400">
                            <span class="text-sm text-sky-700">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-gaf-blue hover:text-gaf-navy font-medium transition-colors">
                            Forgot password?
                        </a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <button type="submit"
                            class="w-full btn-gaf py-3 text-base font-bold rounded-2xl shadow-gaf hover:shadow-gaf-lg group">
                        <svg class="w-5 h-5 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Sign In to Command Platform
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-sky-400 tracking-widest uppercase">
                ⚠ Authorized Access Only • All Activity Logged
            </p>
        </div>
    </div>

</div>
</body>
</html>
