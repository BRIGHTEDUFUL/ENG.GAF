<div wire:poll.60s class="animate-fade-in">

    {{-- ═══ Welcome Banner ═══ --}}
    <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 mb-6 shadow-gaf">
        <div class="absolute inset-0 opacity-10 grid-bg"></div>
        <div class="absolute top-0 right-0 w-64 h-64 rounded-full bg-white/5 blur-2xl"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-block bg-white/10 text-sky-200 text-xs font-semibold px-3 py-1 rounded-full border border-white/20 mb-3">
                    🇬🇭 Ghana Air Force Engineering
                </span>
                <h1 class="text-2xl font-black text-white">
                    Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }},
                    {{ Str::before(auth()->user()->name, ' ') }} 👋
                </h1>
                <p class="text-sky-200 text-sm mt-1">
                    {{ now()->format('l, d F Y') }} · Command Dashboard
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <div class="bg-white/10 backdrop-blur rounded-2xl px-4 py-3 text-center border border-white/20">
                    <p class="text-white text-2xl font-black">{{ now()->format('H:i') }}</p>
                    <p class="text-sky-300 text-xs">Local Time</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ Stats Grid ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        @php
        $stats = [
            ['label'=>'Total Aircraft','value'=>$totalAircraft,'sub'=>$activeAircraft.' active · '.$maintenanceCount.' in maint.',
             'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>',
             'color'=>'from-sky-500 to-blue-600','badge'=>'bg-sky-100 text-sky-700', 'link'=>Route::has('aircraft.index') ? route('aircraft.index') : '#'],
            ['label'=>'Open Tasks','value'=>$openTasks,'sub'=>$criticalTasks > 0 ? '⚠ '.$criticalTasks.' critical' : 'All clear',
             'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>',
             'color'=>'from-amber-500 to-orange-500','badge'=>'bg-amber-100 text-amber-700', 'link'=>Route::has('maintenance.tasks') ? route('maintenance.tasks', ['statusFilter'=>'pending']) : '#'],
            ['label'=>'Incidents','value'=>$openIncidents,'sub'=>$criticalIncidents > 0 ? '⚠ '.$criticalIncidents.' critical' : 'No critical',
             'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
             'color'=>'from-red-500 to-pink-600','badge'=>'bg-red-100 text-red-700', 'link'=>Route::has('incidents.index') ? route('incidents.index', ['statusFilter'=>'open']) : '#'],
            ['label'=>'Personnel','value'=>$totalPersonnel,'sub'=>$activeWings.' active wings',
             'icon'=>'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 100-8 4 4 0 000 8zm6 0a3 3 0 100-6 3 3 0 000 6zM3 14a3 3 0 116 0"/>',
             'color'=>'from-emerald-500 to-teal-600','badge'=>'bg-emerald-100 text-emerald-700', 'link'=>Route::has('personnel.index') ? route('personnel.index') : '#'],
        ];
        @endphp

        @foreach($stats as $i => $stat)
        <a href="{{ $stat['link'] }}" class="block gaf-card p-5 animate-slide-up hover:scale-[1.02] transition-transform duration-200 group" style="animation-delay: {{ $i * 0.1 }}s">
            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br {{ $stat['color'] }} flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        {!! $stat['icon'] !!}
                    </svg>
                </div>
                <span class="badge {{ $stat['badge'] }}">LIVE</span>
            </div>
            <p class="text-3xl font-black text-gaf-navy group-hover:text-gaf-blue transition-colors">{{ $stat['value'] }}</p>
            <p class="text-xs font-semibold text-gray-500 mt-0.5 uppercase tracking-wide">{{ $stat['label'] }}</p>
            <p class="text-xs text-sky-600 mt-2 {{ str_contains($stat['sub'], '⚠') ? 'text-red-500 font-semibold' : '' }}">{{ $stat['sub'] }}</p>
        </a>
        @endforeach
    </div>

    {{-- ═══ Bottom row ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Recent Tasks --}}
        <div class="lg:col-span-2 gaf-card overflow-hidden animate-slide-up" style="animation-delay:0.4s">
            <div class="flex items-center justify-between px-6 py-4 border-b border-sky-50">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-gaf-blue"></div>
                    <h2 class="text-sm font-bold text-gaf-navy">Recent Maintenance Tasks</h2>
                </div>
                @if(Route::has('maintenance.tasks'))
                <a href="{{ route('maintenance.tasks') }}" class="text-xs text-gaf-blue hover:text-gaf-navy font-semibold transition-colors flex items-center gap-1">
                    View all
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                @endif
            </div>
            <div class="divide-y divide-sky-50">
                @forelse($recentTasks as $task)
                @php
                    $pc = ['critical'=>'bg-red-100 text-red-700','high'=>'bg-orange-100 text-orange-700',
                           'medium'=>'bg-amber-100 text-amber-700','low'=>'bg-green-100 text-green-700'];
                    $sc = ['completed'=>'bg-green-100 text-green-700','in-progress'=>'bg-blue-100 text-blue-700',
                           'pending'=>'bg-gray-100 text-gray-600'];
                @endphp
                <div class="flex items-center gap-4 px-6 py-3 trow group">
                    <div class="w-8 h-8 rounded-xl bg-sky-50 flex items-center justify-center shrink-0 group-hover:bg-gaf-blue group-hover:text-white transition-colors">
                        <svg class="w-4 h-4 text-sky-500 group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $task->title }}</p>
                        <p class="text-xs text-sky-500 truncate">{{ $task->aircraft?->tail_number }} · {{ $task->assignedEngineer?->name ?? 'Unassigned' }}</p>
                    </div>
                    <span class="badge {{ $pc[$task->priority] ?? 'bg-gray-100 text-gray-600' }} shrink-0">{{ ucfirst($task->priority) }}</span>
                    <span class="badge {{ $sc[$task->status] ?? 'bg-gray-100 text-gray-600' }} shrink-0">{{ ucfirst($task->status) }}</span>
                </div>
                @empty
                <div class="px-6 py-12 text-center">
                    <svg class="w-10 h-10 text-sky-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-sm text-gray-400">No maintenance tasks yet</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Fleet Status + Incidents --}}
        <div class="gaf-card overflow-hidden animate-slide-up" style="animation-delay:0.5s">
            <div class="px-6 py-4 border-b border-sky-50">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-gaf-sky"></div>
                    <h2 class="text-sm font-bold text-gaf-navy">Fleet Status</h2>
                </div>
            </div>
            <div class="px-6 py-4 space-y-4">
                @foreach($statusBreakdown as $meta)
                <div>
                    <div class="flex justify-between text-xs mb-1.5">
                        <span class="text-gray-600 font-medium">{{ $meta['label'] }}</span>
                        <span class="font-bold text-gaf-navy">{{ $meta['count'] }}</span>
                    </div>
                    <div class="w-full bg-sky-50 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full transition-all duration-1000 {{ $meta['color'] }}"
                             style="width: {{ $meta['pct'] }}%"
                             x-data x-intersect="$el.style.width='{{ $meta['pct'] }}%'"
                             style="width:0%"></div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Recent Incidents --}}
            <div class="border-t border-sky-50 px-6 py-4">
                <h3 class="text-xs font-bold text-gaf-navy uppercase tracking-widest mb-3">Recent Incidents</h3>
                @forelse($recentIncidents as $incident)
                @php
                    $sc2 = ['critical'=>'text-red-500','high'=>'text-orange-500','medium'=>'text-amber-500','low'=>'text-green-500'];
                    $dot = ['critical'=>'bg-red-500','high'=>'bg-orange-500','medium'=>'bg-amber-500','low'=>'bg-green-500'];
                @endphp
                <div class="flex items-start gap-3 mb-3">
                    <span class="mt-1.5 w-2 h-2 rounded-full shrink-0 {{ $dot[$incident->severity] ?? 'bg-gray-400' }}"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs text-gray-700 font-semibold truncate">{{ $incident->title }}</p>
                        <p class="text-xs {{ $sc2[$incident->severity] ?? 'text-gray-400' }} font-medium">{{ ucfirst($incident->severity) }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 italic">No incidents reported.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ═══ Daily State Widget ═══ --}}
    @can('viewAny', App\Models\DailyAircraftState::class)
    @endcan
    <div class="mt-5 gaf-card overflow-hidden animate-slide-up" style="animation-delay:0.6s">
        <div class="flex items-center justify-between px-6 py-4 border-b border-sky-50">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full {{ $dsHasData ? 'bg-green-500 animate-pulse' : 'bg-gray-300' }}"></div>
                <h2 class="text-sm font-bold text-gaf-navy">Today's Serviceability — {{ \Carbon\Carbon::parse($dsToday)->format('d M Y') }}</h2>
            </div>
            @if(Route::has('daily-state.index'))
            <a href="{{ route('daily-state.index') }}" class="text-xs text-gaf-blue hover:text-gaf-navy font-semibold transition-colors flex items-center gap-1">
                Manage
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endif
        </div>
        @if($dsHasData)
        <div class="px-6 py-4">
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="bg-green-50 border border-green-200 rounded-2xl p-3 text-center">
                    <p class="text-2xl font-black text-green-700">{{ $dsServiceable }}</p>
                    <p class="text-xs text-green-600 font-semibold mt-0.5">✅ Serviceable</p>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-2xl p-3 text-center">
                    <p class="text-2xl font-black text-red-700">{{ $dsUnservice }}</p>
                    <p class="text-xs text-red-600 font-semibold mt-0.5">🔴 U/S</p>
                </div>
                <div class="bg-orange-50 border border-orange-200 rounded-2xl p-3 text-center">
                    <p class="text-2xl font-black text-orange-700">{{ $dsCritical }}</p>
                    <p class="text-xs text-orange-600 font-semibold mt-0.5">⚠ Critical</p>
                </div>
            </div>
            @if($dsTotal > 0)
            <div class="w-full bg-sky-50 rounded-full h-3 overflow-hidden">
                <div class="h-3 rounded-full bg-green-500 transition-all duration-1000"
                     style="width: {{ round($dsServiceable / max($dsTotal,1) * 100) }}%"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1.5 text-center">
                {{ round($dsServiceable / max($dsTotal,1) * 100) }}% fleet serviceable today ({{ $dsTotal }} aircraft logged)
            </p>
            @endif
            <div class="mt-4 flex gap-2 justify-center">
                @if(Route::has('daily-state.report'))
                <a href="{{ route('daily-state.report', ['date' => $dsToday]) }}" target="_blank"
                   class="btn-gaf-outline text-xs py-1.5 px-3">
                    🖨 Print AFHQ Report
                </a>
                @endif
                @if(Route::has('daily-state.index'))
                <a href="{{ route('daily-state.index') }}" class="btn-gaf text-xs py-1.5 px-3">
                    + Add / Update
                </a>
                @endif
            </div>
        </div>
        @else
        <div class="px-6 py-8 text-center">
            <svg class="w-10 h-10 text-sky-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <p class="text-sm text-gray-400">No Daily State entries yet for today</p>
            @if(Route::has('daily-state.index'))
            <a href="{{ route('daily-state.index') }}" class="btn-gaf mt-3 inline-flex text-xs">
                Create Today's Report
            </a>
            @endif
        </div>
        @endif
    </div>
</div>
