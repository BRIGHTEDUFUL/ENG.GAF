<div x-data="{toast:{show:false,type:'success',msg:''},showToast(t,m){this.toast={show:true,type:t,msg:m};setTimeout(()=>this.toast.show=false,3500)}}"
     x-on:notify.window="showToast($event.detail.type,$event.detail.message)" class="animate-fade-in">
    <div x-show="toast.show" x-transition.opacity :class="toast.type==='success'?'bg-green-500':'bg-red-500'"
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-white shadow-gaf text-sm font-semibold" style="display:none">
        <span x-text="toast.msg"></span></div>
    <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 mb-6 shadow-gaf">
        <div class="absolute inset-0 opacity-10 grid-bg"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-block bg-white/10 text-sky-200 text-xs font-semibold px-3 py-1 rounded-full border border-white/20 mb-2">🗺 Flight Operations</span>
                <h1 class="text-2xl font-black text-white">Flight Logs</h1>
                <p class="text-sky-200 text-sm mt-1">Mission records, pilot logs, and sortie data</p>
            </div>
            @can('create', App\Models\FlightLog::class)
            <button wire:click="openCreate" class="btn-gaf shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Log Flight
            </button>
            @endcan
        </div>
    </div>
    <div class="gaf-card p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search aircraft or pilot…" class="gaf-input pl-10"/>
            </div>
            <input wire:model.live="dateFilter" type="date" class="gaf-input sm:w-44"/>
            <select wire:model.live="missionFilter" class="gaf-input sm:w-44">
                <option value="">All Missions</option>
                <option value="training">Training</option>
                <option value="patrol">Patrol</option>
                <option value="combat">Combat</option>
                <option value="transport">Transport</option>
                <option value="reconnaissance">Reconnaissance</option>
            </select>
            <button wire:click="exportCsv" class="btn-gaf-outline shrink-0 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </button>
        </div>
    </div>
    <div class="gaf-card overflow-hidden">
        @if($flightLogs->isEmpty())
        <div class="py-16 text-center"><svg class="w-12 h-12 text-sky-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
            <p class="text-gray-400 text-sm">No flight logs found</p></div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gaf-navy to-gaf-blue">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Aircraft</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden md:table-cell">Pilot</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Mission</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Route</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Departure</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Duration</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50">
                    @foreach($flightLogs as $fl)
                    <tr wire:key="fl-{{ $fl->id }}" class="trow">
                        <td class="px-5 py-3 text-sm font-bold text-gaf-navy">{{ $fl->aircraft?->tail_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700 hidden md:table-cell">{{ $fl->pilot?->name ?? '—' }}</td>
                        <td class="px-5 py-3"><span class="badge bg-purple-100 text-purple-700">{{ ucfirst($fl->mission_type ?? '—') }}</span></td>
                        <td class="px-5 py-3 text-xs text-gray-500 hidden lg:table-cell">{{ $fl->departure_location ?? '—' }} → {{ $fl->arrival_location ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500 hidden lg:table-cell whitespace-nowrap">{{ $fl->departure_time?->format('d M H:i') ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm font-semibold text-gaf-blue">{{ $fl->flight_duration_minutes ? round($fl->flight_duration_minutes/60,1).'h' : '—' }}</td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            @can('update', $fl)
                            <button wire:click="openEdit({{ $fl->id }})" class="text-gaf-blue hover:text-gaf-navy text-xs font-semibold mr-3 transition-colors">Edit</button>
                            @endcan
                            @can('delete', $fl)
                            <button wire:click="confirmDelete({{ $fl->id }})" class="text-red-500 hover:text-red-700 text-xs font-semibold transition-colors">Delete</button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-sky-50 bg-sky-50/50">{{ $flightLogs->links() }}</div>
        @endif
    </div>
    @if($showModal)
    <div class="modal-overlay" wire:click.self="$set('showModal',false)">
        <div class="modal-panel" @click.stop>
            <div class="modal-header">
                <div>
                    <h2 class="text-base font-bold">{{ $editingId ? 'Edit Flight Log' : 'Log New Flight' }}</h2>
                    <p class="text-xs text-sky-200 mt-0.5">{{ $editingId ? 'Update sortie details and telemetry' : 'Record a completed sortie, training flight, or mission' }}</p>
                </div>
                <button wire:click="$set('showModal',false)" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body space-y-5">

                {{-- Section: Aircraft & Crew --}}
                <fieldset>
                    <legend class="text-[10px] font-bold text-sky-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="inline-block w-4 h-px bg-sky-300"></span> Aircraft & Crew
                    </legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Aircraft <span class="text-red-400">*</span></label>
                            <select wire:model="form.aircraft_id" class="gaf-input">
                                <option value="">— Select Aircraft —</option>
                                @foreach($aircraft as $ac)<option value="{{ $ac->id }}">{{ $ac->tail_number }} — {{ $ac->model }}</option>@endforeach
                            </select>
                            @error('form.aircraft_id')<p class="mt-1 text-xs text-red-500 flex items-center gap-1">⚠ {{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Pilot in Command <span class="text-red-400">*</span></label>
                            <select wire:model="form.pilot_id" class="gaf-input">
                                <option value="">— Select Pilot —</option>
                                @foreach($pilots as $p)<option value="{{ $p->id }}">{{ $p->rank ? $p->rank.' ' : '' }}{{ $p->name }}</option>@endforeach
                            </select>
                            @error('form.pilot_id')<p class="mt-1 text-xs text-red-500 flex items-center gap-1">⚠ {{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Co-Pilot <span class="text-gray-400 font-normal">(optional)</span></label>
                            <select wire:model="form.co_pilot_id" class="gaf-input">
                                <option value="">— Select Co-Pilot —</option>
                                @foreach($pilots as $p)<option value="{{ $p->id }}">{{ $p->rank ? $p->rank.' ' : '' }}{{ $p->name }}</option>@endforeach
                            </select>
                            @error('form.co_pilot_id')<p class="mt-1 text-xs text-red-500 flex items-center gap-1">⚠ {{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Section: Mission Details --}}
                <fieldset>
                    <legend class="text-[10px] font-bold text-sky-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="inline-block w-4 h-px bg-sky-300"></span> Mission Details
                    </legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Mission Type <span class="text-red-400">*</span></label>
                            <select wire:model="form.mission_type" class="gaf-input">
                                <option value="training">🎯  Training</option>
                                <option value="patrol">👁  Patrol</option>
                                <option value="combat">⚔  Combat</option>
                                <option value="transport">📦  Transport</option>
                                <option value="reconnaissance">🔍  Reconnaissance</option>
                            </select>
                            @error('form.mission_type')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Duration <span class="text-gray-400 font-normal">(auto-calc or manual)</span></label>
                            <div class="relative">
                                <input wire:model="form.flight_duration_minutes" type="number" min="0" class="gaf-input pr-12" placeholder="e.g. 90"/>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">mins</span>
                            </div>
                            @error('form.flight_duration_minutes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Departure Time <span class="text-red-400">*</span></label>
                            <input wire:model="form.departure_time" type="datetime-local" class="gaf-input"/>
                            @error('form.departure_time')<p class="mt-1 text-xs text-red-500 flex items-center gap-1">⚠ {{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Arrival Time <span class="text-red-400">*</span></label>
                            <input wire:model="form.arrival_time" type="datetime-local" class="gaf-input"/>
                            @error('form.arrival_time')<p class="mt-1 text-xs text-red-500 flex items-center gap-1">⚠ {{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Max Altitude <span class="text-gray-400 font-normal">(ft)</span></label>
                            <input wire:model="form.max_altitude_ft" type="number" min="0" class="gaf-input" placeholder="e.g. 25000"/>
                            @error('form.max_altitude_ft')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Max Speed <span class="text-gray-400 font-normal">(knots)</span></label>
                            <input wire:model="form.max_speed_knots" type="number" min="0" class="gaf-input" placeholder="e.g. 450"/>
                            @error('form.max_speed_knots')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Section: Route & Notes --}}
                <fieldset>
                    <legend class="text-[10px] font-bold text-sky-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="inline-block w-4 h-px bg-sky-300"></span> Route & Notes
                    </legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Departure Location</label>
                            <input wire:model="form.departure_location" type="text" class="gaf-input" placeholder="e.g. Kotoka Intl, RWY 21"/>
                            @error('form.departure_location')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Arrival Location</label>
                            <input wire:model="form.arrival_location" type="text" class="gaf-input" placeholder="e.g. Takoradi Air Base"/>
                            @error('form.arrival_location')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Flight Notes</label>
                            <textarea wire:model="form.notes" rows="2" class="gaf-input resize-none" placeholder="Any observations, anomalies, or debrief notes from this sortie…"></textarea>
                            @error('form.notes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

            </div>
            <div class="modal-footer">
                <button wire:click="$set('showModal',false)" class="btn-gaf-outline">Cancel</button>
                <button wire:click="save" wire:loading.attr="disabled" class="btn-gaf">
                    <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Flight Log' : 'Save Flight Log' }}</span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        Saving…
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif
    @if($showDeleteConfirm)
    <div class="modal-overlay">
        <div class="relative z-50 w-full max-w-sm bg-white rounded-2xl shadow-gaf-lg border border-sky-100 overflow-hidden animate-slide-up p-6 text-center">
            <div class="w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-gaf-navy mb-1">Delete Flight Log?</h3>
            <p class="text-sm text-gray-500 mb-6">Removing this log will reverse any aircraft flight-hour adjustments applied. This <strong>cannot be undone</strong>.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteConfirm',false)" class="btn-gaf-outline flex-1">Cancel</button>
                <button wire:click="deleteLog" wire:loading.attr="disabled" class="btn-danger flex-1">
                    <span wire:loading.remove wire:target="deleteLog">Delete</span>
                    <span wire:loading wire:target="deleteLog">Deleting…</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
