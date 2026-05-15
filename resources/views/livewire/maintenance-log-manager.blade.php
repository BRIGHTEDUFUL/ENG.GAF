<div x-data="{toast:{show:false,type:'success',msg:''},showToast(t,m){this.toast={show:true,type:t,msg:m};setTimeout(()=>this.toast.show=false,3500)}}"
     x-on:notify.window="showToast($event.detail.type,$event.detail.message)" class="animate-fade-in">
    <div x-show="toast.show" x-transition.opacity :class="toast.type==='success'?'bg-green-500':'bg-red-500'"
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-white shadow-gaf text-sm font-semibold" style="display:none">
        <span x-text="toast.msg"></span></div>
    <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 mb-6 shadow-gaf">
        <div class="absolute inset-0 opacity-10 grid-bg"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-block bg-white/10 text-sky-200 text-xs font-semibold px-3 py-1 rounded-full border border-white/20 mb-2">📋 Maintenance Records</span>
                <h1 class="text-2xl font-black text-white">Maintenance Logs</h1>
                <p class="text-sky-200 text-sm mt-1">Historical maintenance work records</p>
            </div>
            @can('create', App\Models\MaintenanceLog::class)
            <button wire:click="openCreate" class="btn-gaf shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Log Entry
            </button>
            @endcan
        </div>
    </div>
    <div class="gaf-card p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search logs…" class="gaf-input pl-10"/>
            </div>
            <input wire:model.live="dateFilter" type="date" class="gaf-input sm:w-44"/>
        </div>
    </div>
    <div class="gaf-card overflow-hidden">
        @if($logs->isEmpty())
        <div class="py-16 text-center"><svg class="w-12 h-12 text-sky-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            <p class="text-gray-400 text-sm">No maintenance logs found</p></div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gaf-navy to-gaf-blue">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Date</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Aircraft</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Task</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Engineer</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Hours</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Type</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50">
                    @foreach($logs as $log)
                    <tr wire:key="mlog-{{ $log->id }}" class="trow">
                        <td class="px-5 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $log->performed_at?->format('d M Y') ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm font-bold text-gaf-navy">{{ $log->aircraft?->tail_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700 max-w-xs truncate">{{ $log->maintenanceTask?->title ?? $log->description ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-600 hidden lg:table-cell">{{ $log->engineer?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm font-semibold text-gaf-blue hidden lg:table-cell">{{ $log->hours_spent ? $log->hours_spent.' hrs' : '—' }}</td>
                        <td class="px-5 py-3"><span class="badge bg-sky-100 text-sky-700">{{ ucfirst(str_replace('_',' ',$log->maintenance_type ?? 'general')) }}</span></td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            @can('update', $log)
                            <button wire:click="openEdit({{ $log->id }})" class="text-gaf-blue hover:text-gaf-navy text-xs font-semibold mr-3 transition-colors">Edit</button>
                            @endcan
                            @can('delete', $log)
                            <button wire:click="confirmDelete({{ $log->id }})" class="text-red-500 hover:text-red-700 text-xs font-semibold transition-colors">Delete</button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-sky-50 bg-sky-50/50">{{ $logs->links() }}</div>
        @endif
    </div>
    @if($showModal)
    <div class="modal-overlay" wire:click.self="$set('showModal',false)">
        <div class="modal-panel" @click.stop>
            <div class="modal-header">
                <h2 class="text-base font-bold">{{ $editingId ? 'Edit Log' : 'New Maintenance Log' }}</h2>
                <button wire:click="$set('showModal',false)" class="text-white/70 hover:text-white"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="modal-body">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Aircraft *</label>
                        <select wire:model="aircraft_id" class="gaf-input">
                            <option value="">— Select —</option>
                            @foreach($aircraft as $ac)<option value="{{ $ac->id }}">{{ $ac->tail_number }}</option>@endforeach
                        </select>
                        @error('aircraft_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Date Performed *</label>
                        <input wire:model="performed_at" type="date" class="gaf-input"/>
                        @error('performed_at')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Engineer</label>
                        <select wire:model="engineer_id" class="gaf-input">
                            <option value="">— Select —</option>
                            @foreach($engineers as $eng)<option value="{{ $eng->id }}">{{ $eng->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Hours Spent</label>
                        <input wire:model="hours_spent" type="number" step="0.5" class="gaf-input"/>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Type</label>
                        <select wire:model="maintenance_type" class="gaf-input">
                            <option value="scheduled">Scheduled</option>
                            <option value="unscheduled">Unscheduled</option>
                            <option value="preventive">Preventive</option>
                            <option value="corrective">Corrective</option>
                            <option value="inspection">Inspection</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Task (optional)</label>
                        <select wire:model="maintenance_task_id" class="gaf-input">
                            <option value="">— None —</option>
                            @foreach($tasks as $t)<option value="{{ $t->id }}">{{ Str::limit($t->title,40) }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-sky-700 uppercase tracking-wide mb-1.5">Description *</label>
                    <textarea wire:model="description" rows="3" class="gaf-input resize-none" placeholder="Describe the maintenance work performed…"></textarea>
                    @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="modal-footer">
                <button wire:click="$set('showModal',false)" class="btn-gaf-outline">Cancel</button>
                <button wire:click="save" wire:loading.attr="disabled" class="btn-gaf">
                    <span wire:loading.remove>Save Log</span><span wire:loading>Saving…</span>
                </button>
            </div>
        </div>
    </div>
    @endif
    @if($showDeleteConfirm)
    <div class="modal-overlay">
        <div class="relative z-50 w-full max-w-sm bg-white rounded-2xl shadow-gaf-lg border border-sky-100 overflow-hidden animate-slide-up p-6 text-center">
            <div class="w-12 h-12 rounded-2xl bg-red-100 flex items-center justify-center mx-auto mb-4"><svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>
            <h3 class="text-base font-bold text-gaf-navy mb-1">Delete Log Entry?</h3>
            <p class="text-sm text-gray-500 mb-6">This action cannot be undone.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteConfirm',false)" class="btn-gaf-outline flex-1">Cancel</button>
                <button wire:click="deleteLog" class="btn-danger flex-1">Delete</button>
            </div>
        </div>
    </div>
    @endif
</div>
