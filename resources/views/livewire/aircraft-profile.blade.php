<div class="animate-fade-in pb-8">
    {{-- Header Profile Card --}}
    <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 sm:p-8 mb-6 shadow-gaf flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="absolute inset-0 opacity-10 grid-bg"></div>
        <div class="relative z-10 flex items-center gap-6">
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center shrink-0 shadow-inner">
                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">{{ $aircraft->tail_number }}</h1>
                    @php
                        $sc = ['active'=>'bg-green-500/20 text-green-300 border-green-500/30',
                               'maintenance'=>'bg-amber-500/20 text-amber-300 border-amber-500/30',
                               'grounded'=>'bg-red-500/20 text-red-300 border-red-500/30',
                               'retired'=>'bg-gray-500/20 text-gray-300 border-gray-500/30'];
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $sc[$aircraft->status] ?? 'bg-white/10 text-white' }} uppercase tracking-wider backdrop-blur-md">
                        {{ $aircraft->status }}
                    </span>
                </div>
                <p class="text-sky-200 text-sm font-medium">{{ $aircraft->manufacturer }} {{ $aircraft->model }}</p>
                <div class="flex items-center gap-4 mt-3">
                    <span class="inline-flex items-center gap-1.5 text-xs text-white/80 bg-black/20 px-2.5 py-1 rounded-lg">
                        <svg class="w-3.5 h-3.5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                        {{ $aircraft->wing?->name ?? 'No Wing Assigned' }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs text-white/80 bg-black/20 px-2.5 py-1 rounded-lg">
                        <svg class="w-3.5 h-3.5 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ number_format($aircraft->total_flight_hours, 1) }} Total Hrs
                    </span>
                </div>
            </div>
        </div>
        <div class="relative z-10 shrink-0">
            <a href="{{ route('aircraft.index') }}" class="btn-gaf-outline bg-white/5 border-white/20 text-white hover:bg-white/10 hover:text-white">
                ← Back to Fleet
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column: Serviceability & Defect Summaries --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Next Service Countdown --}}
            <div class="gaf-card p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-sky-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h2 class="font-bold text-gaf-navy">Maintenance Forecast</h2>
                </div>
                
                <div class="mb-2 flex justify-between items-end">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Next 100HR Phase</p>
                        <p class="text-2xl font-black {{ $hoursRemaining <= 10 ? 'text-red-600' : 'text-gaf-blue' }}">
                            {{ number_format($hoursRemaining, 1) }}<span class="text-sm font-semibold text-gray-400 ml-1">hrs left</span>
                        </p>
                    </div>
                </div>
                
                <div class="w-full bg-sky-50 rounded-full h-2.5 overflow-hidden mb-3">
                    <div class="h-2.5 rounded-full transition-all duration-1000 {{ $hoursRemaining <= 10 ? 'bg-red-500' : 'bg-gaf-blue' }}"
                         style="width: {{ $serviceProgress }}%"></div>
                </div>
                <p class="text-xs text-gray-500">Total airframe: <strong>{{ number_format($aircraft->total_flight_hours, 1) }}</strong> hrs</p>
            </div>

            {{-- Open Incidents --}}
            <div class="gaf-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h2 class="font-bold text-gaf-navy">Active Incidents</h2>
                    </div>
                    @if($openIncidents->count() > 0)
                    <span class="badge bg-red-100 text-red-700">{{ $openIncidents->count() }}</span>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse($openIncidents as $inc)
                    @php $severityC = ['critical'=>'text-red-600','high'=>'text-orange-500','medium'=>'text-amber-500','low'=>'text-green-500']; @endphp
                    <div class="p-3 rounded-xl border border-sky-100 bg-sky-50/30">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-semibold text-gray-800 line-clamp-2">{{ $inc->title }}</p>
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $severityC[$inc->severity] ?? 'text-gray-500' }} mt-0.5">{{ $inc->severity }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $inc->incident_date?->format('d M Y') ?? 'Unknown Date' }}</p>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-400 font-medium">No active incidents.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
        </div>

        {{-- Right Column: Timeline & Tasks --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Open Maintenance Tasks --}}
            <div class="gaf-card p-6">
                <div class="flex items-center justify-between mb-4 border-b border-sky-50 pb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <h2 class="font-bold text-gaf-navy">Pending Work Orders</h2>
                    </div>
                    @if(Route::has('maintenance.tasks'))
                    <a href="{{ route('maintenance.tasks') }}?search={{ $aircraft->tail_number }}" class="text-xs font-semibold text-gaf-blue hover:text-gaf-navy transition-colors">Manage Tasks &rarr;</a>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @forelse($openTasks as $task)
                    @php $pC = ['critical'=>'border-red-200 bg-red-50','high'=>'border-orange-200 bg-orange-50','medium'=>'border-amber-200 bg-amber-50','low'=>'border-green-200 bg-green-50']; @endphp
                    <div class="p-4 rounded-xl border {{ $pC[$task->priority] ?? 'border-sky-100 bg-sky-50' }}">
                        <p class="text-sm font-bold text-gray-800 mb-1 truncate">{{ $task->title }}</p>
                        <div class="flex justify-between items-center mt-2">
                            <p class="text-xs font-medium text-gray-600 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                {{ $task->assignedEngineer?->name ?? 'Unassigned' }}
                            </p>
                            <span class="text-[10px] font-bold uppercase text-gray-500">{{ $task->status }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="md:col-span-2 py-6 text-center text-gray-400 text-sm font-medium">
                        All maintenance tasks are complete.
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- 360 Timeline --}}
            <div class="gaf-card p-6">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h2 class="font-bold text-gaf-navy">Activity Timeline</h2>
                </div>

                <div class="relative border-l border-sky-100 ml-3 pl-6 space-y-6">
                    @forelse($timeline as $event)
                    <div class="relative">
                        {{-- Icon Pin --}}
                        <div class="absolute -left-[37px] top-0 w-8 h-8 rounded-full border-4 border-white flex items-center justify-center {{ $event['color'] }} shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $event['icon'] !!}</svg>
                        </div>
                        
                        {{-- Content --}}
                        <div class="bg-sky-50/30 rounded-2xl p-4 border border-sky-50">
                            <div class="flex items-start justify-between gap-4 mb-1">
                                <h3 class="text-sm font-bold text-gray-800">{{ $event['title'] }}</h3>
                                <span class="text-xs font-semibold text-sky-500 whitespace-nowrap">{{ $event['date']?->diffForHumans() ?? 'Unknown' }}</span>
                            </div>
                            <p class="text-xs text-gray-600">{{ $event['desc'] }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 py-4 text-center">No recent activity recorded for this aircraft.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
