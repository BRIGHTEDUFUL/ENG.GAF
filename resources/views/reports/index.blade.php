<x-app-layout>
    <x-slot name="heading">Command Reports</x-slot>

    <div class="animate-fade-in">

        {{-- ═══ Page Header ═══ --}}
        <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 mb-6 shadow-gaf">
            <div class="absolute inset-0 opacity-10 grid-bg"></div>
            <div class="absolute top-0 right-0 w-48 h-48 rounded-full bg-white/5 blur-2xl"></div>
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="inline-block bg-white/10 text-sky-200 text-xs font-semibold px-3 py-1 rounded-full border border-white/20 mb-2">
                        📊 Intelligence & Analytics
                    </span>
                    <h1 class="text-2xl font-black text-white">Command Reports</h1>
                    <p class="text-sky-200 text-sm mt-1">Generate operational intelligence across all modules</p>
                </div>
                <div class="flex items-center gap-2 bg-white/10 border border-white/20 rounded-2xl px-4 py-3">
                    <svg class="w-5 h-5 text-sky-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-white text-sm font-semibold">{{ now()->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        {{-- ═══ Report Cards ═══ --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

            @php
            $reports = [
                [
                    'type'    => 'aircraft',
                    'label'   => 'Aircraft Fleet',
                    'desc'    => 'Current status, wing assignments, and flight hours for the entire fleet.',
                    'color'   => 'from-sky-500 to-blue-600',
                    'bg'      => 'bg-sky-50',
                    'border'  => 'border-sky-200',
                    'text'    => 'text-sky-700',
                    'count'   => \App\Models\Aircraft::count() . ' aircraft',
                    'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
                ],
                [
                    'type'    => 'maintenance',
                    'label'   => 'Maintenance Tasks',
                    'desc'    => 'All maintenance tasks with priority, status, and assigned engineer details.',
                    'color'   => 'from-amber-500 to-orange-500',
                    'bg'      => 'bg-amber-50',
                    'border'  => 'border-amber-200',
                    'text'    => 'text-amber-700',
                    'count'   => \App\Models\MaintenanceTask::count() . ' tasks',
                    'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
                ],
                [
                    'type'    => 'personnel',
                    'label'   => 'Personnel Roster',
                    'desc'    => 'Full command roster with ranks, roles, and wing assignments.',
                    'color'   => 'from-emerald-500 to-teal-600',
                    'bg'      => 'bg-emerald-50',
                    'border'  => 'border-emerald-200',
                    'text'    => 'text-emerald-700',
                    'count'   => \App\Models\User::count() . ' personnel',
                    'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 100-8 4 4 0 000 8zm6 0a3 3 0 100-6 3 3 0 000 6zM3 14a3 3 0 116 0"/>',
                ],
                [
                    'type'    => 'incident',
                    'label'   => 'Incident Report',
                    'desc'    => 'All incidents with severity classification and investigation status.',
                    'color'   => 'from-red-500 to-pink-600',
                    'bg'      => 'bg-red-50',
                    'border'  => 'border-red-200',
                    'text'    => 'text-red-700',
                    'count'   => \App\Models\Incident::count() . ' incidents',
                    'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                ],
                [
                    'type'    => 'flight_ops',
                    'label'   => 'Flight Operations',
                    'desc'    => 'Complete flight log with mission types, pilots, and duration data.',
                    'color'   => 'from-purple-500 to-violet-600',
                    'bg'      => 'bg-purple-50',
                    'border'  => 'border-purple-200',
                    'text'    => 'text-purple-700',
                    'count'   => \App\Models\FlightLog::count() . ' flights',
                    'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>',
                ],
                [
                    'type'    => 'daily_state',
                    'label'   => 'Daily Aircraft State',
                    'desc'    => 'AFHQ-format daily operational readiness report grouped by wing with defects and next service.',
                    'color'   => 'from-gaf-navy to-gaf-blue',
                    'bg'      => 'bg-sky-50',
                    'border'  => 'border-sky-200',
                    'text'    => 'text-sky-700',
                    'count'   => \App\Models\DailyAircraftState::whereDate('report_date', today())->count() . ' today',
                    'special' => true,
                    'link'    => route('daily-state.report', ['date' => today()->toDateString()]),
                    'icon'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>',
                ],
            ];
            @endphp

            @foreach($reports as $i => $r)
            <a href="{{ isset($r['special']) && $r['special'] ? $r['link'] : route('reports.generate', ['type' => $r['type']]) }}"
               target="{{ isset($r['special']) && $r['special'] ? '_blank' : '_self' }}"
               class="gaf-card p-6 block group animate-slide-up"
               style="animation-delay: {{ $i * 0.08 }}s">
                <div class="flex items-start justify-between mb-5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $r['color'] }} flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            {!! $r['icon'] !!}
                        </svg>
                    </div>
                    <span class="badge {{ $r['bg'] }} {{ $r['text'] }} border {{ $r['border'] }}">
                        {{ $r['count'] ?? '' }}
                    </span>
                </div>

                <h3 class="text-base font-bold text-gaf-navy group-hover:text-gaf-blue transition-colors">
                    {{ $r['label'] }}
                </h3>
                <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ $r['desc'] }}</p>

                <div class="flex items-center gap-2 mt-5 text-xs font-semibold text-gaf-blue group-hover:gap-3 transition-all duration-200">
                    <span>Generate Report</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </a>
            @endforeach

        </div>
    </div>
</x-app-layout>
