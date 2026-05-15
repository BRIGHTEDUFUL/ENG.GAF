<div x-data="{toast:{show:false,type:'success',msg:''},showToast(t,m){this.toast={show:true,type:t,msg:m};setTimeout(()=>this.toast.show=false,4000)}}"
     x-on:notify.window="showToast($event.detail.type,$event.detail.message)" class="animate-fade-in">

    <div x-show="toast.show" x-transition.opacity :class="toast.type==='success'?'bg-green-500':'bg-red-500'"
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-white shadow-gaf text-sm font-semibold" style="display:none">
        <span x-text="toast.msg"></span></div>

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 mb-5 shadow-gaf">
        <div class="absolute inset-0 opacity-10 grid-bg"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-block bg-white/10 text-sky-200 text-xs font-semibold px-3 py-1 rounded-full border border-white/20 mb-2">📋 AFHQ Ops</span>
                <h1 class="text-2xl font-black text-white">Daily Aircraft State</h1>
                <p class="text-sky-200 text-sm mt-1">AFHQ Daily Aircraft &amp; Vehicle State — {{ \Carbon\Carbon::parse($reportDate)->format('d M Y') }}</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('daily-state.report', ['date' => $reportDate]) }}" target="_blank"
                   class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print Report
                </a>
                <button wire:click="autoPopulate" wire:loading.attr="disabled"
                        class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span wire:loading.remove>Auto-Populate</span>
                    <span wire:loading>Populating…</span>
                </button>
                <button wire:click="openCreate" class="btn-gaf shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Entry
                </button>
            </div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
        @foreach([[$todayS,'Serviceable','bg-green-50 border-green-200 text-green-700','✅'],[$todayUS,'Unserviceable','bg-red-50 border-red-200 text-red-700','🔴'],[$todayGND,'Grounded','bg-gray-50 border-gray-200 text-gray-600','⛔'],[$critical,'Critical Defects','bg-orange-50 border-orange-200 text-orange-700','⚠']] as [$val,$lbl,$cls,$ico])
        <div class="bg-white rounded-2xl border {{ explode(' ',$cls)[1] }} p-4 text-center animate-slide-up">
            <p class="text-3xl font-black {{ explode(' ',$cls)[2] }}">{{ $val }}</p>
            <p class="text-xs font-semibold text-gray-500 mt-1">{{ $ico }} {{ $lbl }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="gaf-card p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <input wire:model.live="reportDate" type="date" class="gaf-input sm:w-48"/>
            <select wire:model.live="wingFilter" class="gaf-input sm:w-56">
                <option value="">All Wings</option>
                @foreach($wings as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
            </select>
        </div>
    </div>

    {{-- Grouped table --}}
    @if($grouped->isEmpty())
    <div class="gaf-card py-16 text-center">
        <svg class="w-14 h-14 text-sky-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p class="text-gray-400 font-medium">No entries for {{ \Carbon\Carbon::parse($reportDate)->format('d M Y') }}</p>
        <p class="text-xs text-sky-400 mt-2">Click <strong>Auto-Populate</strong> to carry forward yesterday's U/S aircraft, or <strong>Add Entry</strong> manually.</p>
    </div>
    @else
    <div class="gaf-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-gaf-navy to-gaf-blue">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider w-24">Reg</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">Dy Hrs</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">Tot Hrs</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider hidden md:table-cell">Dy Lgs</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider hidden md:table-cell">Tot Lgs</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">State</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Nature of Defect</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden xl:table-cell">Next Svc / Remarks</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grouped as $wingName => $states)
                    {{-- Wing section header --}}
                    <tr class="bg-sky-50 border-y border-sky-200">
                        <td colspan="10" class="px-4 py-2 text-xs font-black text-gaf-navy uppercase tracking-widest">
                            {{ strtoupper($wingName) }}
                        </td>
                    </tr>
                    @foreach($states as $state)
                    <tr wire:key="state-{{ $state->id }}"
                        class="border-b border-sky-50 hover:bg-sky-50/50 transition-colors {{ $state->has_critical_defect ? 'border-l-4 border-l-red-400' : '' }}">
                        <td class="px-4 py-3 font-bold text-gaf-navy text-sm">
                            {{ $state->aircraft?->tail_number ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">{{ $state->aircraft?->model ?? '—' }}</td>
                        <td class="px-4 py-3 text-center text-sm font-semibold text-gaf-blue">
                            {{ $state->daily_flight_hrs > 0 ? number_format($state->daily_flight_hrs,2) : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600">
                            {{ $state->total_flight_hrs ? number_format($state->total_flight_hrs,2) : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600 hidden md:table-cell">
                            {{ $state->daily_landings > 0 ? $state->daily_landings : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600 hidden md:table-cell">
                            {{ $state->total_landings ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge border {{ $state->state_badge_color }} font-bold {{ $state->state === 'U/S' ? 'animate-pulse' : '' }}">
                                {{ $state->display_state }}
                            </span>
                        </td>
                        <td class="px-4 py-3 max-w-xs">
                            @if($state->defects->isNotEmpty())
                                @foreach($state->defects->take(2) as $d)
                                <p class="text-xs {{ $d->is_critical ? 'text-red-600 font-semibold' : 'text-gray-600' }} leading-snug">
                                    {{ $d->roman_numeral }}. {{ Str::limit($d->description, 55) }}
                                </p>
                                @endforeach
                                @if($state->defects->count() > 2)
                                <span class="badge bg-orange-100 text-orange-700 mt-1">+{{ $state->defects->count()-2 }} more</span>
                                @endif
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 max-w-xs hidden xl:table-cell">
                            @if($state->serviceRemarks->isNotEmpty())
                                @foreach($state->serviceRemarks->take(2) as $r)
                                <p class="text-xs text-sky-700 leading-snug">
                                    {{ $r->roman_numeral }}. {{ Str::limit($r->description, 50) }}
                                    @if($r->due_hours)<span class="font-semibold">({{ number_format($r->due_hours,0) }}hrs)</span>@endif
                                </p>
                                @endforeach
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <button wire:click="openEdit({{ $state->id }})" class="text-gaf-blue hover:text-gaf-navy text-xs font-semibold mr-2 transition-colors">Edit</button>
                            <button wire:click="confirmDelete({{ $state->id }})" class="text-red-500 hover:text-red-700 text-xs font-semibold transition-colors">Del</button>
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Entry Modal --}}
    @if($showModal)
    <div class="modal-overlay" wire:click.self="$set('showModal',false)">
        <div class="relative z-50 w-full max-w-3xl bg-white rounded-3xl shadow-gaf-lg overflow-hidden animate-slide-up" @click.stop>
            <div class="modal-header">
                <h2 class="text-base font-bold">{{ $editingStateId ? 'Edit Daily State Entry' : 'New Daily State Entry' }}</h2>
                <button wire:click="$set('showModal',false)" class="text-white/70 hover:text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="max-h-[75vh] overflow-y-auto">
                {{-- Section 1: Aircraft --}}
                <div class="px-6 pt-5 pb-3 border-b border-sky-50">
                    <p class="text-xs font-black text-sky-700 uppercase tracking-widest mb-3">Aircraft Details</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Aircraft *</label>
                            <select wire:model.live="form.aircraft_id" class="gaf-input">
                                <option value="">— Select —</option>
                                @foreach($aircraft as $ac)<option value="{{ $ac->id }}">{{ $ac->tail_number }} ({{ $ac->model }})</option>@endforeach
                            </select>
                            @error('form.aircraft_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Wing</label>
                            <input type="text" class="gaf-input bg-sky-50" readonly
                                   value="{{ collect($aircraft)->firstWhere('id', $form['aircraft_id'])?->wing?->name ?? '—' }}"/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">State *</label>
                            <select wire:model="form.state" class="gaf-input">
                                <option value="S">S — Serviceable</option>
                                <option value="U/S">U/S — Unserviceable</option>
                                <option value="grounded">GND — Grounded</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Flight Data --}}
                <div class="px-6 py-4 border-b border-sky-50">
                    <p class="text-xs font-black text-sky-700 uppercase tracking-widest mb-3">Flight Data</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Daily Hrs</label>
                            <input wire:model="form.daily_flight_hrs" type="number" step="0.05" min="0" max="99" class="gaf-input" placeholder="0.00"/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Total Hrs</label>
                            <input wire:model="form.total_flight_hrs" type="number" step="0.05" min="0" class="gaf-input"/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Daily Lgs</label>
                            <input wire:model="form.daily_landings" type="number" min="0" class="gaf-input" placeholder="0"/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Total Lgs</label>
                            <input wire:model="form.total_landings" type="number" min="0" class="gaf-input"/>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Defects --}}
                <div class="px-6 py-4 border-b border-sky-50">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-black text-sky-700 uppercase tracking-widest">Nature of Defect</p>
                        <button type="button" wire:click="addDefect"
                                class="text-xs font-semibold text-gaf-blue hover:text-gaf-navy flex items-center gap-1 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Defect
                        </button>
                    </div>
                    <div class="space-y-2">
                        @foreach($defects as $i => $defect)
                        <div class="flex items-start gap-2">
                            <span class="mt-2.5 text-xs font-bold text-gaf-navy w-6 shrink-0">{{ ['I','II','III','IV','V','VI','VII','VIII','IX','X'][$i] ?? ($i+1) }}.</span>
                            <textarea wire:model="defects.{{ $i }}.description" rows="1"
                                      class="gaf-input flex-1 resize-none text-sm"
                                      placeholder="Describe defect…"
                                      x-on:input="$el.style.height='auto';$el.style.height=$el.scrollHeight+'px'"></textarea>
                            <label class="flex items-center gap-1 mt-2.5 shrink-0 cursor-pointer">
                                <input type="checkbox" wire:model="defects.{{ $i }}.is_critical" class="rounded border-red-300 text-red-500"/>
                                <span class="text-xs text-red-500 font-semibold">Critical</span>
                            </label>
                            @if(count($defects) > 1)
                            <button type="button" wire:click="removeDefect({{ $i }})" class="mt-2 text-red-400 hover:text-red-600 shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Section 4: Service Remarks --}}
                <div class="px-6 py-4 border-b border-sky-50">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-black text-sky-700 uppercase tracking-widest">Next Svc / Remarks / R.I.E</p>
                        <button type="button" wire:click="addRemark"
                                class="text-xs font-semibold text-gaf-blue hover:text-gaf-navy flex items-center gap-1 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add Remark
                        </button>
                    </div>
                    <div class="space-y-2">
                        @foreach($remarks as $i => $remark)
                        <div class="flex items-start gap-2">
                            <span class="mt-2.5 text-xs font-bold text-gaf-navy w-6 shrink-0">{{ ['I','II','III','IV','V','VI','VII','VIII','IX','X'][$i] ?? ($i+1) }}.</span>
                            <input wire:model="remarks.{{ $i }}.description" type="text" class="gaf-input flex-1 text-sm" placeholder="e.g. 25HR DUE AT 2975HRS"/>
                            <input wire:model="remarks.{{ $i }}.due_hours" type="number" step="1" class="gaf-input w-24 text-sm" placeholder="Hrs"/>
                            <input wire:model="remarks.{{ $i }}.service_location" type="text" class="gaf-input w-28 text-sm" placeholder="Location"/>
                            @if(count($remarks) > 1)
                            <button type="button" wire:click="removeRemark({{ $i }})" class="mt-2 text-red-400 hover:text-red-600 shrink-0">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Notes --}}
                <div class="px-6 py-4">
                    <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">General Notes</label>
                    <textarea wire:model="form.notes" rows="2" class="gaf-input resize-none" placeholder="Additional remarks…"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button wire:click="$set('showModal',false)" class="btn-gaf-outline">Cancel</button>
                <button wire:click="save" wire:loading.attr="disabled" class="btn-gaf">
                    <span wire:loading.remove>Save Entry</span><span wire:loading>Saving…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirm --}}
    @if($showDeleteConfirm)
    <div class="modal-overlay">
        <div class="relative z-50 w-full max-w-sm bg-white rounded-2xl shadow-gaf-lg border border-sky-100 p-6 text-center animate-slide-up">
            <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-gaf-navy mb-1">Delete Entry?</h3>
            <p class="text-sm text-gray-500 mb-6">This action cannot be undone.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteConfirm',false)" class="btn-gaf-outline flex-1">Cancel</button>
                <button wire:click="deleteState" class="btn-danger flex-1">Delete</button>
            </div>
        </div>
    </div>
    @endif
</div>
