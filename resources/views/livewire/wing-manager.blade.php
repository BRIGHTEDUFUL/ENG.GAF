<div x-data="{toast:{show:false,type:'success',msg:''},showToast(t,m){this.toast={show:true,type:t,msg:m};setTimeout(()=>this.toast.show=false,3500)}}"
     x-on:notify.window="showToast($event.detail.type,$event.detail.message)"
     class="animate-fade-in">

    {{-- Toast --}}
    <div x-show="toast.show" x-transition.opacity
         :class="toast.type==='success' ? 'bg-green-500' : 'bg-red-500'"
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-white shadow-gaf text-sm font-semibold" style="display:none">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-show="toast.type==='success'" d="M5 13l4 4L19 7"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-show="toast.type!=='success'" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <span x-text="toast.msg"></span>
    </div>

    {{-- Page Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 mb-6 shadow-gaf">
        <div class="absolute inset-0 opacity-10 grid-bg"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-block bg-white/10 text-sky-200 text-xs font-semibold px-3 py-1 rounded-full border border-white/20 mb-2">🪖 Command Structure</span>
                <h1 class="text-2xl font-black text-white">Wings Management</h1>
                <p class="text-sky-200 text-sm mt-1">Manage wing units, commanders and base locations</p>
            </div>
            @can('create', App\Models\Wing::class)
            <button wire:click="openCreate" class="btn-gaf shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Wing
            </button>
            @endcan
        </div>
    </div>

    {{-- Search --}}
    <div class="mb-4">
        <div class="relative w-full sm:w-80">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search wings…" class="gaf-input pl-10"/>
        </div>
    </div>

    {{-- Table --}}
    <div class="gaf-card overflow-hidden">
        @if($wings->isEmpty())
        <div class="py-16 text-center">
            <svg class="w-12 h-12 text-sky-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
            <p class="text-gray-400 text-sm font-medium">No wings found</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gaf-navy to-gaf-blue">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Wing</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden md:table-cell">Base Location</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Commander</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Aircraft</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50">
                    @foreach($wings as $wing)
                    <tr wire:key="wing-{{ $wing->id }}" class="trow group">
                        <td class="px-5 py-3">
                            <p class="text-sm font-bold text-gaf-navy">{{ $wing->name }}</p>
                            <p class="text-xs text-sky-500 font-mono">{{ $wing->code }}</p>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600 hidden md:table-cell">{{ $wing->base_location }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700 hidden lg:table-cell">{{ $wing->commander?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm font-semibold text-gaf-blue hidden lg:table-cell">{{ $wing->aircraft_count }}</td>
                        <td class="px-5 py-3">
                            <span class="badge {{ $wing->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ ucfirst($wing->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            @can('update', $wing)
                            <button wire:click="openEdit({{ $wing->id }})" class="text-gaf-blue hover:text-gaf-navy text-xs font-semibold mr-3 transition-colors">Edit</button>
                            @endcan
                            @can('delete', $wing)
                            <button wire:click="confirmDelete({{ $wing->id }})" class="text-red-500 hover:text-red-700 text-xs font-semibold transition-colors">Delete</button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-sky-50 bg-sky-50/50">{{ $wings->links() }}</div>
        @endif
    </div>

    {{-- Create / Edit Modal --}}
    @if($showModal)
    <div class="modal-overlay" wire:click.self="$set('showModal',false)">
        <div class="modal-panel" @click.stop>
            <div class="modal-header">
                <h2 class="text-base font-bold">{{ $editingId ? 'Edit Wing' : 'New Wing' }}</h2>
                <button wire:click="$set('showModal',false)" class="text-white/70 hover:text-white">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Wing Name *</label>
                        <input wire:model="form.name" type="text" class="gaf-input" placeholder="1st Fighter Wing"/>
                        @error('form.name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Code *</label>
                        <input wire:model="form.code" type="text" class="gaf-input" placeholder="1FW"/>
                        @error('form.code')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Base Location *</label>
                    <input wire:model="form.base_location" type="text" class="gaf-input" placeholder="Accra Air Base"/>
                    @error('form.base_location')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Commander</label>
                        <select wire:model="form.commander_id" class="gaf-input">
                            <option value="">— None —</option>
                            @foreach($commanders as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Status</label>
                        <select wire:model="form.status" class="gaf-input">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Description</label>
                    <textarea wire:model="form.description" rows="2" class="gaf-input resize-none" placeholder="Wing description…"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button wire:click="$set('showModal',false)" class="btn-gaf-outline">Cancel</button>
                <button wire:click="save" wire:loading.attr="disabled" class="btn-gaf">
                    <span wire:loading.remove>Save Wing</span>
                    <span wire:loading>Saving…</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Confirm --}}
    @if($showDeleteConfirm)
    <div class="modal-overlay">
        <div class="relative z-50 w-full max-w-sm bg-white rounded-2xl shadow-gaf-lg border border-sky-100 overflow-hidden animate-slide-up">
            <div class="p-6">
                <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </div>
                <h3 class="text-base font-bold text-gaf-navy text-center mb-1">Delete Wing?</h3>
                <p class="text-sm text-gray-500 text-center mb-6">This action cannot be undone.</p>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteConfirm',false)" class="btn-gaf-outline flex-1">Cancel</button>
                    <button wire:click="deleteWing" class="btn-danger flex-1">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
