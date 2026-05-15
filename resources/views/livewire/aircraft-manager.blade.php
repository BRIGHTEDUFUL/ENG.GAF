<div x-data="{toast:{show:false,type:'success',msg:''},showToast(t,m){this.toast={show:true,type:t,msg:m};setTimeout(()=>this.toast.show=false,3500)}}"
     x-on:notify.window="showToast($event.detail.type,$event.detail.message)" class="animate-fade-in">
    <div x-show="toast.show" x-transition.opacity :class="toast.type==='success'?'bg-green-500':'bg-red-500'"
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-white shadow-gaf text-sm font-semibold" style="display:none">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span x-text="toast.msg"></span>
    </div>
    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 mb-6 shadow-gaf">
        <div class="absolute inset-0 opacity-10 grid-bg"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-block bg-white/10 text-sky-200 text-xs font-semibold px-3 py-1 rounded-full border border-white/20 mb-2">✈ Fleet Management</span>
                <h1 class="text-2xl font-black text-white">Aircraft Fleet</h1>
                <p class="text-sky-200 text-sm mt-1">Manage aircraft inventory, status and wing assignments</p>
            </div>
            @can('create', App\Models\Aircraft::class)
            <button wire:click="openCreate" class="btn-gaf shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Aircraft
            </button>
            @endcan
        </div>
    </div>
    {{-- Filters --}}
    <div class="gaf-card p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search tail number or model…" class="gaf-input pl-10"/>
            </div>
            <select wire:model.live="statusFilter" class="gaf-input sm:w-44">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="maintenance">Maintenance</option>
                <option value="grounded">Grounded</option>
                <option value="retired">Retired</option>
            </select>
            <button wire:click="exportCsv" class="btn-gaf-outline shrink-0 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </button>
        </div>
    </div>
    {{-- Table --}}
    <div class="gaf-card overflow-hidden">
        @if($aircraft->isEmpty())
        <div class="py-16 text-center">
            <svg class="w-12 h-12 text-sky-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            <p class="text-gray-400 text-sm">No aircraft found</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gaf-navy to-gaf-blue">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Aircraft</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden md:table-cell">Wing</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Flight Hours</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Last Maint.</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50">
                    @foreach($aircraft as $ac)
                    @php $sc=['active'=>'bg-green-100 text-green-700','maintenance'=>'bg-amber-100 text-amber-700','grounded'=>'bg-red-100 text-red-700','retired'=>'bg-gray-100 text-gray-500']; @endphp
                    <tr wire:key="ac-{{ $ac->id }}" class="trow">
                        <td class="px-5 py-3">
                            <a href="{{ route('aircraft.profile', $ac->id) }}" wire:navigate class="text-sm font-bold text-gaf-navy hover:text-gaf-blue transition-colors">
                                {{ $ac->tail_number }}
                            </a>
                            <p class="text-xs text-sky-500">{{ $ac->model }} · {{ $ac->manufacturer }}</p>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600 hidden md:table-cell">{{ $ac->wing?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm font-semibold text-gaf-blue hidden lg:table-cell">{{ number_format($ac->total_flight_hours,1) }} hrs</td>
                        <td class="px-5 py-3 text-sm text-gray-500 hidden lg:table-cell">{{ $ac->last_maintenance_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-5 py-3"><span class="badge {{ $sc[$ac->status] ?? 'bg-gray-100 text-gray-500' }}">{{ ucfirst($ac->status) }}</span></td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            @can('update', $ac)
                            <button wire:click="openEdit({{ $ac->id }})" class="text-gaf-blue hover:text-gaf-navy text-xs font-semibold mr-3 transition-colors">Edit</button>
                            @endcan
                            @can('delete', $ac)
                            <button wire:click="confirmDelete({{ $ac->id }})" class="text-red-500 hover:text-red-700 text-xs font-semibold transition-colors">Delete</button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-sky-50 bg-sky-50/50">{{ $aircraft->links() }}</div>
        @endif
    </div>
    {{-- Modal --}}
    @if($showModal)
    <div class="modal-overlay" wire:click.self="$set('showModal',false)">
        <div class="modal-panel" @click.stop>
            <div class="modal-header">
                <div>
                    <h2 class="text-base font-bold">{{ $editingId ? 'Edit Aircraft' : 'Register Aircraft' }}</h2>
                    <p class="text-xs text-sky-200 mt-0.5">{{ $editingId ? 'Update aircraft record' : 'Add a new aircraft to the fleet registry' }}</p>
                </div>
                <button wire:click="$set('showModal',false)" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body space-y-5">

                {{-- Section: Identity --}}
                <fieldset>
                    <legend class="text-[10px] font-bold text-sky-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="inline-block w-4 h-px bg-sky-300"></span> Aircraft Identity
                    </legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tail Number <span class="text-red-400">*</span></label>
                            <input wire:model="tail_number" type="text" class="gaf-input font-mono" placeholder="GAF-001"/>
                            @error('tail_number')<p class="mt-1 text-xs text-red-500 flex items-center gap-1">⚠ {{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Aircraft Model <span class="text-red-400">*</span></label>
                            <input wire:model="model" type="text" class="gaf-input" placeholder="e.g. C-27J Spartan"/>
                            @error('model')<p class="mt-1 text-xs text-red-500 flex items-center gap-1">⚠ {{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Manufacturer</label>
                            <input wire:model="manufacturer" type="text" class="gaf-input" placeholder="e.g. Alenia Aermacchi"/>
                            @error('manufacturer')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Year Manufactured</label>
                            <input wire:model="year_manufactured" type="number" class="gaf-input" placeholder="{{ date('Y') }}" min="1940" max="{{ date('Y') }}"/>
                            @error('year_manufactured')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Section: Assignment & Status --}}
                <fieldset>
                    <legend class="text-[10px] font-bold text-sky-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="inline-block w-4 h-px bg-sky-300"></span> Assignment & Status
                    </legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Wing Assignment</label>
                            <select wire:model="wing_id" class="gaf-input">
                                <option value="">— Unassigned —</option>
                                @foreach($wings as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
                            </select>
                            @error('wing_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Operational Status <span class="text-red-400">*</span></label>
                            <select wire:model="status" class="gaf-input">
                                <option value="active">✅  Active</option>
                                <option value="maintenance">🔧  In Maintenance</option>
                                <option value="grounded">⛔  Grounded</option>
                                <option value="retired">🚫  Retired</option>
                            </select>
                            @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Section: Operations --}}
                <fieldset>
                    <legend class="text-[10px] font-bold text-sky-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="inline-block w-4 h-px bg-sky-300"></span> Operations & Maintenance
                    </legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Total Flight Hours</label>
                            <div class="relative">
                                <input wire:model="total_flight_hours" type="number" step="0.1" min="0" class="gaf-input pr-10" placeholder="0.0"/>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none">hrs</span>
                            </div>
                            @error('total_flight_hours')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Last Maintenance Date</label>
                            <input wire:model="last_maintenance_date" type="date" class="gaf-input"/>
                            @error('last_maintenance_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Notes / Remarks</label>
                    <textarea wire:model="notes" rows="3" class="gaf-input resize-none" placeholder="Any additional remarks about this aircraft…"></textarea>
                    @error('notes')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

            </div>
            <div class="modal-footer">
                <button wire:click="$set('showModal',false)" class="btn-gaf-outline">Cancel</button>
                <button wire:click="save" wire:loading.attr="disabled" class="btn-gaf">
                    <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Aircraft' : 'Register Aircraft' }}</span>
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
        <div class="relative z-50 w-full max-w-sm bg-white rounded-2xl shadow-gaf-lg border border-sky-100 overflow-hidden animate-slide-up">
            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-base font-bold text-gaf-navy mb-1">Delete Aircraft?</h3>
                <p class="text-sm text-gray-500 mb-6">All associated maintenance tasks, logs, and flight records will be unlinked. This <strong>cannot be undone</strong>.</p>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteConfirm',false)" class="btn-gaf-outline flex-1">Cancel</button>
                    <button wire:click="deleteAircraft" wire:loading.attr="disabled" class="btn-danger flex-1">
                        <span wire:loading.remove wire:target="deleteAircraft">Delete</span>
                        <span wire:loading wire:target="deleteAircraft">Deleting…</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

