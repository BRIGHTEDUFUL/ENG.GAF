<div x-data="{toast:{show:false,type:'success',msg:''},showToast(t,m){this.toast={show:true,type:t,msg:m};setTimeout(()=>this.toast.show=false,3500)}}"
     x-on:notify.window="showToast($event.detail.type,$event.detail.message)" class="animate-fade-in">
    <div x-show="toast.show" x-transition.opacity :class="toast.type==='success'?'bg-green-500':'bg-red-500'"
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-white shadow-gaf text-sm font-semibold" style="display:none">
        <span x-text="toast.msg"></span></div>
    <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 mb-6 shadow-gaf">
        <div class="absolute inset-0 opacity-10 grid-bg"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-block bg-white/10 text-sky-200 text-xs font-semibold px-3 py-1 rounded-full border border-white/20 mb-2">⚠ Incident Management</span>
                <h1 class="text-2xl font-black text-white">Incidents</h1>
                <p class="text-sky-200 text-sm mt-1">Track, investigate and resolve operational incidents</p>
            </div>
            @can('create', App\Models\Incident::class)
            <button wire:click="openCreate" class="btn-gaf shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Report Incident
            </button>
            @endcan
        </div>
    </div>
    <div class="gaf-card p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search incidents…" class="gaf-input pl-10"/>
            </div>
            <select wire:model.live="severityFilter" class="gaf-input sm:w-40">
                <option value="">All Severities</option>
                <option value="critical">Critical</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>
            <select wire:model.live="statusFilter" class="gaf-input sm:w-44">
                <option value="">All Statuses</option>
                <option value="open">Open</option>
                <option value="under-investigation">Investigating</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
            <button wire:click="exportCsv" class="btn-gaf-outline shrink-0 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </button>
        </div>
    </div>
    <div class="gaf-card overflow-hidden">
        @if($incidents->isEmpty())
        <div class="py-16 text-center"><svg class="w-12 h-12 text-sky-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p class="text-gray-400 text-sm">No incidents found</p></div>
        @else
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gaf-navy to-gaf-blue">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Incident</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden md:table-cell">Date</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Aircraft</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Reported By</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Severity</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50">
                    @foreach($incidents as $inc)
                    @php
                        $sc=['critical'=>'bg-red-100 text-red-700','high'=>'bg-orange-100 text-orange-700','medium'=>'bg-amber-100 text-amber-700','low'=>'bg-green-100 text-green-700'];
                        $stc=['open'=>'bg-red-100 text-red-700','under-investigation'=>'bg-blue-100 text-blue-700','resolved'=>'bg-green-100 text-green-700','closed'=>'bg-gray-100 text-gray-600'];
                    @endphp
                    <tr wire:key="inc-desktop-{{ $inc->id }}" class="trow {{ $inc->severity==='critical' ? 'border-l-4 border-red-400' : '' }}">
                        <td class="px-5 py-3">
                            <p class="text-sm font-semibold text-gaf-navy max-w-xs truncate">{{ $inc->title }}</p>
                            @if($inc->description)<p class="text-xs text-gray-400 truncate max-w-xs">{{ Str::limit($inc->description,60) }}</p>@endif
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-500 whitespace-nowrap hidden md:table-cell">{{ $inc->incident_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-sky-700 font-medium hidden lg:table-cell">{{ $inc->aircraft?->tail_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-600 hidden lg:table-cell">{{ $inc->reporter?->name ?? '—' }}</td>
                        <td class="px-5 py-3"><span class="badge {{ $sc[$inc->severity] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($inc->severity) }}</span></td>
                        <td class="px-5 py-3"><span class="badge {{ $stc[$inc->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst(str_replace('-',' ',$inc->status)) }}</span></td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            @can('update', $inc)
                            <button wire:click="openEdit({{ $inc->id }})" class="text-gaf-blue hover:text-gaf-navy text-xs font-semibold mr-3 transition-colors">Edit</button>
                            @endcan
                            @can('delete', $inc)
                            <button wire:click="confirmDelete({{ $inc->id }})" class="text-red-500 hover:text-red-700 text-xs font-semibold transition-colors">Delete</button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Card Stack --}}
        <div class="md:hidden divide-y divide-sky-50">
            @foreach($incidents as $inc)
            @php
                $sc=['critical'=>'bg-red-100 text-red-700','high'=>'bg-orange-100 text-orange-700','medium'=>'bg-amber-100 text-amber-700','low'=>'bg-green-100 text-green-700'];
                $stc=['open'=>'bg-red-100 text-red-700','under-investigation'=>'bg-blue-100 text-blue-700','resolved'=>'bg-green-100 text-green-700','closed'=>'bg-gray-100 text-gray-600'];
            @endphp
            <div class="p-4 {{ $inc->severity==='critical' ? 'bg-red-50/30' : '' }}">
                <div class="flex justify-between items-start gap-2 mb-2">
                    <p class="text-sm font-bold text-gaf-navy">{{ $inc->title }}</p>
                    <span class="badge {{ $sc[$inc->severity] ?? 'bg-gray-100 text-gray-600' }} shrink-0">{{ ucfirst($inc->severity) }}</span>
                </div>
                
                <p class="text-xs text-sky-700 font-semibold mb-1">Aircraft: {{ $inc->aircraft?->tail_number ?? '—' }}</p>
                <p class="text-xs text-gray-500 mb-3">Date: {{ $inc->incident_date?->format('d M Y') ?? '—' }}</p>
                
                <div class="flex items-center justify-between border-t border-sky-100/50 pt-3">
                    <span class="badge {{ $stc[$inc->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst(str_replace('-',' ',$inc->status)) }}</span>
                    
                    <div class="flex items-center gap-3">
                        @can('update', $inc)
                        <button wire:click="openEdit({{ $inc->id }})" class="text-gaf-blue font-bold text-sm px-2 py-1 bg-sky-50 rounded-lg">Edit</button>
                        @endcan
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-5 py-3 border-t border-sky-50 bg-sky-50/50">{{ $incidents->links() }}</div>
        @endif
    </div>
    @if($showModal)
    <div class="modal-overlay" wire:click.self="$set('showModal',false)">
        <div class="modal-panel" @click.stop>
            <div class="modal-header">
                <div>
                    <h2 class="text-base font-bold">{{ $editingId ? 'Edit Incident' : 'Report Incident' }}</h2>
                    <p class="text-xs text-sky-200 mt-0.5">{{ $editingId ? 'Update incident details and status' : 'Document a new operational incident for investigation' }}</p>
                </div>
                <button wire:click="$set('showModal',false)" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body space-y-5">

                {{-- Section: Core Details --}}
                <fieldset>
                    <legend class="text-[10px] font-bold text-sky-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="inline-block w-4 h-px bg-sky-300"></span> Incident Details
                    </legend>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Incident Title <span class="text-red-400">*</span></label>
                            <input wire:model="form.title" type="text" class="gaf-input" placeholder="Brief, descriptive title of what occurred"/>
                            @error('form.title')<p class="mt-1 text-xs text-red-500 flex items-center gap-1">⚠ {{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                            <textarea wire:model="form.description" rows="3" class="gaf-input resize-none" placeholder="Detailed account of the incident — what happened, when, how it was discovered…"></textarea>
                            @error('form.description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Resolution Notes <span class="text-gray-400 font-normal">(optional)</span></label>
                            <textarea wire:model="form.resolution_notes" rows="2" class="gaf-input resize-none" placeholder="Notes on how this incident was resolved…"></textarea>
                            @error('form.resolution_notes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Section: Classification --}}
                <fieldset>
                    <legend class="text-[10px] font-bold text-sky-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="inline-block w-4 h-px bg-sky-300"></span> Classification & Timing
                    </legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Severity <span class="text-red-400">*</span></label>
                            <select wire:model="form.severity" class="gaf-input">
                                <option value="low">🟢  Low</option>
                                <option value="medium">🟡  Medium</option>
                                <option value="high">🟠  High</option>
                                <option value="critical">🔴  Critical</option>
                            </select>
                            @error('form.severity')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-400">*</span></label>
                            <select wire:model="form.status" class="gaf-input">
                                <option value="open">🔴  Open</option>
                                <option value="under-investigation">🔵  Under Investigation</option>
                                <option value="resolved">🟢  Resolved</option>
                                <option value="closed">⚫  Closed</option>
                            </select>
                            @error('form.status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Incident Date <span class="text-red-400">*</span></label>
                            <input wire:model="form.incident_date" type="date" class="gaf-input"/>
                            @error('form.incident_date')<p class="mt-1 text-xs text-red-500">⚠ {{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Location</label>
                            <input wire:model="form.location" type="text" class="gaf-input" placeholder="e.g. Hangar 3, Runway 09L"/>
                            @error('form.location')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Section: Assignment --}}
                <fieldset>
                    <legend class="text-[10px] font-bold text-sky-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="inline-block w-4 h-px bg-sky-300"></span> Aircraft & Personnel
                    </legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Aircraft Involved</label>
                            <select wire:model="form.aircraft_id" class="gaf-input">
                                <option value="">— None / Unknown —</option>
                                @foreach($aircraft as $ac)<option value="{{ $ac->id }}">{{ $ac->tail_number }} — {{ $ac->model }}</option>@endforeach
                            </select>
                            @error('form.aircraft_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Assigned Investigator</label>
                            <select wire:model="form.assigned_investigator_id" class="gaf-input">
                                <option value="">— Unassigned —</option>
                                @foreach($investigators as $p)<option value="{{ $p->id }}">{{ $p->rank ? $p->rank.' ' : '' }}{{ $p->name }}</option>@endforeach
                            </select>
                            @error('form.assigned_investigator_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

            </div>
            <div class="modal-footer">
                <button wire:click="$set('showModal',false)" class="btn-gaf-outline">Cancel</button>
                <button wire:click="save" wire:loading.attr="disabled" class="btn-gaf">
                    <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Incident' : 'Report Incident' }}</span>
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
            <div class="w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center mx-auto mb-4"><svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
            <h3 class="text-base font-bold text-gaf-navy mb-1">Delete Incident?</h3>
            <p class="text-sm text-gray-500 mb-6">The incident record and all investigation notes will be permanently removed. This <strong>cannot be undone</strong>.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteConfirm',false)" class="btn-gaf-outline flex-1">Cancel</button>
                <button wire:click="deleteIncident" wire:loading.attr="disabled" class="btn-danger flex-1">
                    <span wire:loading.remove wire:target="deleteIncident">Delete</span>
                    <span wire:loading wire:target="deleteIncident">Deleting…</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

