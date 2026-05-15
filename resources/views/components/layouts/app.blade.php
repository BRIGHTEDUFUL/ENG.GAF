@props(['heading' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $heading ? $heading.' | ' : '' }}Ghana Air Force Engineering Command System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-sky-50 text-gray-800">

<div x-data="{ sidebarOpen: window.innerWidth >= 1024, sidebarCollapsed: false }" class="flex h-screen overflow-hidden">

    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen && window.innerWidth < 1024"
         x-transition.opacity @click="sidebarOpen=false"
         class="fixed inset-0 z-20 bg-gaf-navy/50 backdrop-blur-sm lg:hidden print:hidden" style="display:none"></div>

    {{-- ═══ SIDEBAR ═══ --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-30 flex flex-col bg-gaf-gradient shadow-gaf-lg transition-transform duration-300 lg:static lg:translate-x-0 print:hidden"
           :style="sidebarCollapsed ? 'width:72px' : 'width:260px'">

        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10 shrink-0">
            <div class="flex items-center justify-center w-12 h-12 rounded-full bg-white shrink-0 p-1.5 shadow-sm">
                <img src="{{ asset('images/gaf-logo.png') }}" alt="GAF Logo" class="w-full h-full object-contain">
            </div>
            <div class="min-w-0 overflow-hidden" x-show="!sidebarCollapsed">
                <p class="text-white font-bold text-sm leading-tight">Ghana Air Force</p>
                <p class="text-sky-300 text-xs">Engineering Command</p>
            </div>
            <button @click="sidebarCollapsed=!sidebarCollapsed"
                    class="hidden lg:flex ml-auto text-white/50 hover:text-white transition-colors shrink-0 p-1 rounded-lg hover:bg-white/10">
                <svg class="w-4 h-4 transition-transform" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
            @php
            $navItems = [
                ['route'=>'dashboard',         'label'=>'Dashboard',       'roles'=>null,                         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2V7zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V7zm0 8a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2zM3 15a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2z"/>'],
                ['route'=>'personnel.index',   'label'=>'Personnel',       'roles'=>null,                         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 100-8 4 4 0 000 8zm6 0a3 3 0 100-6 3 3 0 000 6zM3 14a3 3 0 116 0"/>'],
                ['route'=>'wings.index',       'label'=>'Wings',           'roles'=>['admin','commander'],        'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>'],
                ['route'=>'aircraft.index',    'label'=>'Aircraft',        'roles'=>['admin','commander','supervisor','auditor'], 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>'],
                ['route'=>'maintenance.tasks', 'label'=>'Maint. Tasks',    'roles'=>null,                         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                ['route'=>'maintenance.logs',  'label'=>'Maint. Logs',     'roles'=>null,                         'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'],
                ['route'=>'flight-logs.index', 'label'=>'Flight Logs',     'roles'=>['admin','commander','auditor'], 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>'],
                ['route'=>'incidents.index',   'label'=>'Incidents',       'roles'=>['admin','commander','supervisor'], 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
                ['route'=>'audit-logs.index',  'label'=>'Audit Logs',      'roles'=>['admin','auditor'],           'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
                ['route'=>'daily-state.index',  'label'=>'Daily State',     'roles'=>['admin','commander','supervisor','auditor'], 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>'],
                ['route'=>'reports.index',     'label'=>'Reports',         'roles'=>['admin','commander','auditor'], 'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
            ];
            @endphp

            @foreach($navItems as $item)
            @php
                $allowed = $item['roles'] === null || (auth()->check() && in_array(auth()->user()->role, $item['roles']));
                $isActive = Route::has($item['route']) && request()->routeIs(
                    str_replace(['.index','.tasks','.logs','.generate'], '', $item['route']).'*'
                );
            @endphp
            @if($allowed && Route::has($item['route']))
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group
                      {{ $isActive ? 'bg-white/20 text-white' : 'text-sky-100 hover:bg-white/10 hover:text-white' }}"
               title="{{ $item['label'] }}">
                <svg class="w-5 h-5 shrink-0 transition-transform duration-200 group-hover:scale-110
                            {{ $isActive ? 'text-white' : 'text-sky-300 group-hover:text-white' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    {!! $item['icon'] !!}
                </svg>
                <span class="truncate" x-show="!sidebarCollapsed">{{ $item['label'] }}</span>
                @if($isActive)
                <span class="ml-auto w-1.5 h-1.5 rounded-full bg-white shrink-0" x-show="!sidebarCollapsed"></span>
                @endif
            </a>
            @endif
            @endforeach
        </nav>

        {{-- User footer --}}
        @auth
        <div class="px-3 py-4 border-t border-white/10 shrink-0">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-white/15 text-white text-xs font-bold shrink-0 border border-white/10">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0" x-show="!sidebarCollapsed">
                    <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
                    <p class="text-sky-300 text-xs truncate">
                        {{ auth()->user()->rank ? auth()->user()->rank.' · ' : '' }}{{ ucfirst(auth()->user()->role) }}
                    </p>
                </div>
                <form method="POST" action="{{ route('logout') }}" x-show="!sidebarCollapsed">
                    @csrf
                    <button type="submit" title="Logout"
                            class="text-sky-300 hover:text-white transition-colors p-1.5 hover:bg-white/10 rounded-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </aside>

    {{-- ═══ MAIN ═══ --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden print:overflow-visible">

        {{-- Top bar --}}
        <header class="flex items-center gap-3 h-16 px-4 sm:px-6 bg-white border-b border-sky-100 shadow-sm shrink-0 print:hidden">
            <button @click="sidebarOpen=!sidebarOpen"
                    class="lg:hidden p-2 rounded-xl text-sky-600 hover:bg-sky-50 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Breadcrumb --}}
            <div class="hidden sm:flex items-center gap-2 text-sm">
                <span class="text-sky-400">🇬🇭 GAF</span>
                <svg class="w-3 h-3 text-sky-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                @if($heading)
                <span class="font-semibold text-gaf-navy truncate max-w-xs">{{ $heading }}</span>
                @endif
            </div>

            <div class="flex items-center gap-3 ml-auto">
                {{-- Global Search --}}
                <div class="mr-2">
                    @livewire('global-search')
                </div>

                @livewire('notification-panel')

                @auth
                @php
                    $roleColors=['admin'=>'bg-purple-100 text-purple-700','commander'=>'bg-blue-100 text-blue-700',
                                 'supervisor'=>'bg-amber-100 text-amber-700','engineer'=>'bg-green-100 text-green-700',
                                 'auditor'=>'bg-sky-100 text-sky-700'];
                    $rc = $roleColors[auth()->user()->role] ?? 'bg-gray-100 text-gray-700';
                @endphp
                <span class="hidden md:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $rc }}">
                    {{ strtoupper(auth()->user()->role) }}
                </span>
                <div class="w-8 h-8 rounded-xl bg-gaf-gradient flex items-center justify-center text-white text-xs font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                @endauth
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-6 bg-sky-50">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
