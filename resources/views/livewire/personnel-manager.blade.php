<div x-data="{toast:{show:false,type:'success',msg:''},showToast(t,m){this.toast={show:true,type:t,msg:m};setTimeout(()=>this.toast.show=false,3500)}}"
     x-on:notify.window="showToast($event.detail.type,$event.detail.message)" class="animate-fade-in">
    <div x-show="toast.show" x-transition.opacity :class="toast.type==='success'?'bg-green-500':'bg-red-500'"
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-white shadow-gaf text-sm font-semibold" style="display:none">
        <span x-text="toast.msg"></span></div>
    <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 mb-6 shadow-gaf">
        <div class="absolute inset-0 opacity-10 grid-bg"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-block bg-white/10 text-sky-200 text-xs font-semibold px-3 py-1 rounded-full border border-white/20 mb-2">👥 Personnel Division</span>
                <h1 class="text-2xl font-black text-white">Personnel Roster</h1>
                <p class="text-sky-200 text-sm mt-1">Manage command personnel, ranks and assignments</p>
            </div>
            @can('create', App\Models\Personnel::class)
            <button wire:click="openCreate" class="btn-gaf shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Personnel
            </button>
            @endcan
        </div>
    </div>
    <div class="gaf-card p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search personnel…" class="gaf-input pl-10"/>
            </div>
            <select wire:model.live="roleFilter" class="gaf-input sm:w-44">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="commander">Commander</option>
                <option value="supervisor">Supervisor</option>
                <option value="engineer">Engineer</option>
                <option value="auditor">Auditor</option>
            </select>
            @if(isset($wings))
            <select wire:model.live="wingFilter" class="gaf-input sm:w-44">
                <option value="">All Wings</option>
                @foreach($wings as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
            </select>
            @endif
            <button wire:click="exportCsv" class="btn-gaf-outline shrink-0 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </button>
        </div>
    </div>
    <div class="gaf-card overflow-hidden">
        @if($personnel->isEmpty())
        <div class="py-16 text-center"><svg class="w-12 h-12 text-sky-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-4a4 4 0 100-8 4 4 0 000 8zm6 0a3 3 0 100-6 3 3 0 000 6zM3 14a3 3 0 116 0"/></svg>
            <p class="text-gray-400 text-sm">No personnel found</p></div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gaf-navy to-gaf-blue">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden md:table-cell">Role</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Rank</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Wing</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden xl:table-cell">Email</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50">
                    @foreach($personnel as $person)
                    @php
                        $roleC=['admin'=>'bg-purple-100 text-purple-700','commander'=>'bg-blue-100 text-blue-700','supervisor'=>'bg-amber-100 text-amber-700','engineer'=>'bg-green-100 text-green-700','auditor'=>'bg-sky-100 text-sky-700'];
                    @endphp
                    <tr wire:key="p-{{ $person->id }}" class="trow">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-gaf-gradient flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ strtoupper(substr($person->name ?? $person->first_name ?? '?', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gaf-navy">{{ $person->full_name ?? ($person->first_name.' '.($person->last_name ?? '')) }}</p>
                                    <p class="text-xs text-sky-500 xl:hidden">{{ $person->email ?? $person->user?->email ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 hidden md:table-cell">
                            @php $role = $person->role ?? $person->user?->role ?? null; @endphp
                            @if($role)<span class="badge {{ $roleC[$role] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($role) }}</span>@else<span class="text-gray-400 text-xs">—</span>@endif
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600 hidden lg:table-cell">{{ $person->rank ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-600 hidden lg:table-cell">{{ $person->wing?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs text-sky-600 hidden xl:table-cell">{{ $person->email ?? $person->user?->email ?? '—' }}</td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            @can('update', $person)
                            <button wire:click="openEdit({{ $person->id }})" class="text-gaf-blue hover:text-gaf-navy text-xs font-semibold mr-3 transition-colors">Edit</button>
                            @endcan
                            @can('delete', $person)
                            <button wire:click="confirmDelete({{ $person->id }})" class="text-red-500 hover:text-red-700 text-xs font-semibold transition-colors">Delete</button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-sky-50 bg-sky-50/50">{{ $personnel->links() }}</div>
        @endif
    </div>
    @if($showModal)
    <div class="modal-overlay" wire:click.self="$set('showModal',false)">
        <div class="modal-panel" @click.stop>
            <div class="modal-header">
                <h2 class="text-base font-bold">{{ $editingId ? 'Edit Personnel' : 'Add Personnel' }}</h2>
                <button wire:click="$set('showModal',false)" class="text-white/70 hover:text-white"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="modal-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Full Name *</label>
                        <input wire:model="form.name" type="text" class="gaf-input"/>
                        @error('form.name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Email Address *</label>
                        <input wire:model="form.email" type="email" class="gaf-input"/>
                        @error('form.email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Password</label>
                        <input wire:model="form.password" type="password" class="gaf-input" placeholder="Leave blank for default/unchanged"/>
                        @error('form.password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Rank</label>
                        <input wire:model="form.rank" type="text" class="gaf-input" placeholder="e.g. Flight Lieutenant"/>
                        @error('form.rank')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">System Role</label>
                        <select wire:model="form.role" class="gaf-input">
                            <option value="engineer">Engineer</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="commander">Commander</option>
                            <option value="auditor">Auditor</option>
                            <option value="admin">Admin</option>
                        </select>
                        @error('form.role')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Wing Assignment</label>
                        <select wire:model="form.wing_id" class="gaf-input">
                            <option value="">— None (Unassigned) —</option>
                            @if(isset($wings))@foreach($wings as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach@endif
                        </select>
                        @error('form.wing_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button wire:click="$set('showModal',false)" class="btn-gaf-outline">Cancel</button>
                <button wire:click="save" wire:loading.attr="disabled" class="btn-gaf">
                    <span wire:loading.remove>Save Personnel</span><span wire:loading>Saving…</span>
                </button>
            </div>
        </div>
    </div>
    @endif
    @if($showDeleteConfirm)
    <div class="modal-overlay">
        <div class="relative z-50 w-full max-w-sm bg-white rounded-2xl shadow-gaf-lg border border-sky-100 overflow-hidden animate-slide-up p-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center mx-auto mb-4"><svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
            <h3 class="text-base font-bold text-gaf-navy mb-1">Remove Personnel?</h3>
            <p class="text-sm text-gray-500 mb-6">This action cannot be undone.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteConfirm',false)" class="btn-gaf-outline flex-1">Cancel</button>
                <button wire:click="deletePersonnel" wire:loading.attr="disabled" class="btn-danger flex-1">
                    <span wire:loading.remove wire:target="deletePersonnel">Remove</span>
                    <span wire:loading wire:target="deletePersonnel">Removing...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
