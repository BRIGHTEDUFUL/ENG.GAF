<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? ($heading ?? 'GAF-ECS') }} · Ghana Air Force</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-[#F0F7FF] text-gray-800">

<div x-data="{
        sidebarOpen: window.innerWidth >= 1024,
        sidebarCollapsed: false,
        get sidebarWidth() { return this.sidebarCollapsed ? '72px' : '260px'; }
     }"
     class="flex h-screen overflow-hidden">

    {{-- Mobile backdrop --}}
    <div x-show="sidebarOpen && window.innerWidth < 1024"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen=false"
         class="fixed inset-0 z-20 bg-gaf-navy/60 backdrop-blur-sm lg:hidden print:hidden"
         style="display:none">
    </div>

    {{-- ════════════════════════ SIDEBAR ════════════════════════ --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           :style="`width:${sidebarWidth}`"
           class="fixed inset-y-0 left-0 z-30 flex flex-col
                  transition-all duration-300 ease-out
                  lg:static lg:translate-x-0 print:hidden
                  bg-gradient-to-b from-[#0A2558] via-[#0B4FA3] to-[#0C7BC8]
                  shadow-[4px_0_24px_rgba(11,45,107,0.4)]">

        {{-- Logo area --}}
        <div class="flex items-center gap-3 px-4 py-4 border-b border-white/10 shrink-0">
            <div class="flex items-center justify-center w-11 h-11 rounded-2xl bg-white/10
                        border border-white/20 shrink-0 p-1.5 backdrop-blur-sm
                        transition-all duration-300 hover:bg-white/20">
                <img src="{{ asset('images/gaf-logo.png') }}" alt="GAF" class="w-full h-full object-contain drop-shadow-sm">
            </div>
            <div class="min-w-0 flex-1 overflow-hidden transition-all duration-300" x-show="!sidebarCollapsed" x-cloak>
                <p class="text-white font-bold text-sm leading-tight">Ghana Air Force</p>
                <p class="text-sky-300/80 text-[11px] mt-0.5 font-medium tracking-wide">Engineering Command</p>
            </div>
            <button @click="sidebarCollapsed=!sidebarCollapsed"
                    class="hidden lg:flex ml-auto p-1.5 text-white/40 hover:text-white
                           hover:bg-white/10 rounded-xl transition-all duration-200 shrink-0">
                <svg class="w-3.5 h-3.5 transition-transform duration-300"
                     :class="sidebarCollapsed ? 'rotate-180' : ''"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-2.5 py-3 space-y-0.5">
            @php
            $navGroups = [
                'Operations' => [
                    ['route' => 'dashboard',         'label' => 'Dashboard',     'roles' => null,
                     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h4a2 2 0 012 2v4a2 2 0 01-2 2H5a2 2 0 01-2-2V7zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V7zm0 8a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2zM3 15a2 2 0 012-2h4a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2z"/>'],
                    ['route' => 'daily-state.index', 'label' => 'Daily State',   'roles' => null,
                     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
                    ['route' => 'incidents.index',   'label' => 'Incidents',     'roles' => ['admin','commander','supervisor'],
                     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
                    ['route' => 'reports.index',     'label' => 'Reports',       'roles' => ['admin','commander','auditor'],
                     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
                ],
                'Fleet & Maintenance' => [
                    ['route' => 'aircraft.index',    'label' => 'Aircraft',      'roles' => ['admin','commander','supervisor','auditor'],
                     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>'],
                    ['route' => 'maintenance.tasks', 'label' => 'Maint. Tasks',  'roles' => null,
                     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>'],
                    ['route' => 'maintenance.logs',  'label' => 'Maint. Logs',   'roles' => null,
                     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'],
                    ['route' => 'flight-logs.index', 'label' => 'Flight Logs',   'roles' => ['admin','commander','auditor'],
                     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>'],
                ],
                'Administration' => [
                    ['route' => 'personnel.index',   'label' => 'Personnel',     'roles' => null,
                     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 100-8 4 4 0 000 8zm6 0a3 3 0 100-6 3 3 0 000 6zM3 14a3 3 0 116 0"/>'],
                    ['route' => 'wings.index',        'label' => 'Wings',         'roles' => ['admin','commander'],
                     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>'],
                    ['route' => 'audit-logs.index',  'label' => 'Audit Logs',    'roles' => ['admin','auditor'],
                     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'],
                    ['route' => 'system-data.index', 'label' => 'System Data',   'roles' => ['admin','commander'],
                     'icon'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>'],
                ],
            ];
            @endphp

            @foreach($navGroups as $groupName => $navItems)
            <p x-show="!sidebarCollapsed" x-cloak
               class="px-2 pt-5 pb-1.5 text-[9px] font-black text-white/30 uppercase tracking-[0.2em] select-none">
                {{ $groupName }}
            </p>
            <div x-show="sidebarCollapsed" class="my-2 mx-2 border-t border-white/10"></div>

            @foreach($navItems as $item)
            @php
                $allowed = $item['roles'] === null || in_array(auth()->user()->role, $item['roles']);
                $routeBase = preg_replace('/\.(index|tasks|logs)$/', '', $item['route']);
                $active = Route::has($item['route']) && request()->routeIs($routeBase.'*');
            @endphp
            @if($allowed && Route::has($item['route']))
            <a href="{{ route($item['route']) }}"
               class="sidebar-link relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium
                      transition-all duration-200 group
                      {{ $active
                            ? 'bg-white/15 text-white shadow-sm'
                            : 'text-sky-100/70 hover:bg-white/8 hover:text-white' }}"
               title="{{ $item['label'] }}"
               wire:navigate>

                {{-- Active bar indicator --}}
                @if($active)
                <span class="absolute right-0 top-1/2 -translate-y-1/2 w-0.5 h-6 rounded-l-full bg-white opacity-80"></span>
                @endif

                <svg class="w-5 h-5 shrink-0 transition-all duration-200
                            {{ $active ? 'text-white' : 'text-sky-300/70 group-hover:text-white group-hover:scale-110' }}"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    {!! $item['icon'] !!}
                </svg>
                <span x-show="!sidebarCollapsed" x-cloak class="truncate flex-1">{{ $item['label'] }}</span>
                @if($active)
                <span x-show="!sidebarCollapsed" x-cloak
                      class="ml-auto w-1.5 h-1.5 rounded-full bg-sky-300 shrink-0 animate-pulse-slow"></span>
                @endif
            </a>
            @endif
            @endforeach
            @endforeach
        </nav>

        {{-- User footer --}}
        <div class="px-3 py-3.5 border-t border-white/10 shrink-0">
            <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-white/8 transition-colors cursor-default group">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl
                            bg-gradient-to-br from-sky-400 to-blue-600
                            text-white text-xs font-bold shrink-0 shadow-sm
                            group-hover:shadow-md transition-shadow">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0" x-show="!sidebarCollapsed" x-cloak>
                    <p class="text-white text-xs font-semibold truncate leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-sky-300/70 text-[11px] truncate mt-0.5">
                        {{ Auth::user()->rank ? Auth::user()->rank.' · ' : '' }}{{ ucfirst(Auth::user()->role) }}
                    </p>
                </div>
                <form method="POST" action="{{ route('logout') }}" x-show="!sidebarCollapsed" x-cloak>
                    @csrf
                    <button type="submit" title="Logout"
                            class="p-1.5 text-sky-300/50 hover:text-white hover:bg-white/10
                                   rounded-lg transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ════════════════════════ MAIN CONTENT ════════════════════════ --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden print:overflow-visible">

        {{-- Top bar --}}
        <header class="flex items-center gap-3 h-16 px-4 sm:px-6
                       bg-white/90 backdrop-blur-md border-b border-slate-100
                       shadow-[0_1px_12px_rgba(0,0,0,0.06)] shrink-0 print:hidden z-10">

            {{-- Mobile menu button --}}
            <button @click="sidebarOpen=!sidebarOpen"
                    class="lg:hidden p-2 rounded-xl text-slate-500 hover:text-gaf-blue
                           hover:bg-sky-50 transition-all duration-150 active:scale-95">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page title / breadcrumb --}}
            <div class="flex items-center gap-2 min-w-0">
                <span class="hidden sm:block text-slate-400 text-xs">🇬🇭</span>
                <span class="hidden sm:block text-slate-300 text-xs">/</span>
                @isset($heading)
                <h1 class="text-sm font-bold text-gaf-navy truncate">{{ $heading }}</h1>
                @endisset
            </div>

            {{-- Right side controls --}}
            <div class="flex items-center gap-2 ml-auto">

                {{-- Notification bell --}}
                <livewire:notification-panel />

                {{-- Role badge --}}
                @php
                    $roleData = [
                        'admin'      => ['class' => 'bg-purple-50 text-purple-700 border-purple-200',  'icon' => '👑'],
                        'commander'  => ['class' => 'bg-blue-50   text-blue-700   border-blue-200',    'icon' => '⭐'],
                        'supervisor' => ['class' => 'bg-amber-50  text-amber-700  border-amber-200',   'icon' => '🔧'],
                        'engineer'   => ['class' => 'bg-emerald-50 text-emerald-700 border-emerald-200','icon' => '⚙'],
                        'auditor'    => ['class' => 'bg-sky-50    text-sky-700    border-sky-200',     'icon' => '🔍'],
                    ];
                    $rd = $roleData[Auth::user()->role] ?? ['class' => 'bg-gray-100 text-gray-600 border-gray-200', 'icon' => '•'];
                @endphp
                <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                             text-xs font-semibold border {{ $rd['class'] }}">
                    <span>{{ $rd['icon'] }}</span>
                    {{ strtoupper(Auth::user()->role) }}
                </span>

                {{-- Avatar with dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open=!open"
                            class="flex items-center justify-center w-9 h-9 rounded-xl
                                   bg-gradient-to-br from-gaf-blue to-gaf-sky
                                   text-white text-xs font-bold shadow-sm
                                   hover:shadow-gaf hover:scale-105
                                   transition-all duration-200 active:scale-95">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </button>
                    <div x-show="open"
                         x-cloak
                         @click.outside="open=false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-11 w-52 bg-white rounded-2xl
                                shadow-gaf-lg border border-slate-100 overflow-hidden z-50">
                        <div class="px-4 py-3 bg-gradient-to-r from-sky-50 to-blue-50 border-b border-slate-100">
                            <p class="text-sm font-bold text-gaf-navy truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-600
                                      hover:bg-sky-50 hover:text-gaf-blue transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                My Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm
                                               text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6">
            {{ $slot }}
        </main>
    </div>
</div>

{{-- ══════════ Toast Notification System ══════════ --}}
<div x-data="toastSystem()"
     @notify.window="add($event.detail)"
     class="fixed bottom-5 right-5 z-[60] flex flex-col gap-2 pointer-events-none">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-6 scale-95"
             x-transition:enter-end="opacity-100 translate-x-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0 scale-100"
             x-transition:leave-end="opacity-0 translate-x-6 scale-95"
             class="pointer-events-auto flex items-start gap-3 px-4 py-3.5
                    rounded-2xl shadow-gaf-lg border text-sm font-medium
                    min-w-[280px] max-w-sm bg-white"
             :class="{
                 'border-emerald-200': toast.type === 'success',
                 'border-red-200':     toast.type === 'error',
                 'border-amber-200':   toast.type === 'warning',
                 'border-sky-200':     toast.type === 'info',
             }">
            {{-- Icon --}}
            <span class="flex items-center justify-center w-7 h-7 rounded-xl shrink-0 mt-px text-base"
                  :class="{
                      'bg-emerald-50': toast.type === 'success',
                      'bg-red-50':     toast.type === 'error',
                      'bg-amber-50':   toast.type === 'warning',
                      'bg-sky-50':     toast.type === 'info',
                  }"
                  x-text="toast.type === 'success' ? '✓' : toast.type === 'error' ? '✕' : toast.type === 'warning' ? '⚠' : 'ℹ'">
            </span>
            <div class="flex-1 min-w-0">
                <p x-text="toast.message" class="text-slate-700 leading-snug"></p>
            </div>
            <button @click="dismiss(toast.id)"
                    class="shrink-0 text-slate-400 hover:text-slate-600 transition-colors p-0.5 -mt-0.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>

@livewireScripts
<script>
function toastSystem() {
    return {
        toasts: [],
        add(detail) {
            const id = Date.now();
            const type = detail.type ?? 'info';
            const message = detail.message ?? '';
            this.toasts.push({ id, type, message, show: false });
            this.$nextTick(() => {
                const t = this.toasts.find(t => t.id === id);
                if (t) t.show = true;
            });
            setTimeout(() => this.dismiss(id), 5000);
        },
        dismiss(id) {
            const t = this.toasts.find(t => t.id === id);
            if (t) { t.show = false; setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 250); }
        }
    };
}
</script>
</body>
</html>
