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
                <span class="inline-block bg-white/10 text-sky-200 text-xs font-semibold px-3 py-1 rounded-full border border-white/20 mb-2">🔧 Maintenance Control</span>
                <h1 class="text-2xl font-black text-white">Maintenance Tasks</h1>
                <p class="text-sky-200 text-sm mt-1">Track and manage all maintenance work orders</p>
            </div>
            @can('create', App\Models\MaintenanceTask::class)
            <button wire:click="openCreate" class="btn-gaf shrink-0">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Task
            </button>
            @endcan
        </div>
    </div>
    {{-- Filters --}}
    <div class="gaf-card p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search tasks…" class="gaf-input pl-10"/>
            </div>
            <select wire:model.live="priorityFilter" class="gaf-input sm:w-40">
                <option value="">All Priorities</option>
                <option value="critical">Critical</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>
            <select wire:model.live="statusFilter" class="gaf-input sm:w-40">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="in-progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>
    </div>
    {{-- Table --}}
    <div class="gaf-card overflow-hidden">
        @if($tasks->isEmpty())
        <div class="py-16 text-center">
            <svg class="w-12 h-12 text-sky-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p class="text-gray-400 text-sm">No maintenance tasks found</p>
        </div>
        @else
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gaf-navy to-gaf-blue">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Task</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden md:table-cell">Aircraft</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Assigned To</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Due Date</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Priority</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50">
                    @foreach($tasks as $task)
                    @php
                        $pc=['critical'=>'bg-red-100 text-red-700','high'=>'bg-orange-100 text-orange-700','medium'=>'bg-amber-100 text-amber-700','low'=>'bg-green-100 text-green-700'];
                        $sc=['completed'=>'bg-green-100 text-green-700','in-progress'=>'bg-blue-100 text-blue-700','pending'=>'bg-gray-100 text-gray-600'];
                    @endphp
                    <tr wire:key="task-desktop-{{ $task->id }}" class="trow {{ $task->is_overdue ? 'border-l-4 border-red-400' : '' }}">
                        <td class="px-5 py-3">
                            <p class="text-sm font-semibold text-gaf-navy">{{ $task->title }}</p>
                            @if($task->is_overdue)<p class="text-xs text-red-500 font-semibold">⚠ Overdue</p>@endif
                        </td>
                        <td class="px-5 py-3 text-sm text-sky-700 font-medium hidden md:table-cell">{{ $task->aircraft?->tail_number ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-600 hidden lg:table-cell">{{ $task->assignedEngineer?->name ?? 'Unassigned' }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500 hidden lg:table-cell {{ $task->is_overdue ? 'text-red-500 font-semibold' : '' }}">{{ $task->due_date?->format('d M Y') ?? '—' }}</td>
                        <td class="px-5 py-3"><span class="badge {{ $pc[$task->priority] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($task->priority) }}</span></td>
                        <td class="px-5 py-3"><span class="badge {{ $sc[$task->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst(str_replace('-',' ',$task->status)) }}</span></td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            @can('update', $task)
                            @if($task->status !== 'completed')
                            <button wire:click="logAndClose({{ $task->id }})" class="text-green-600 hover:text-green-800 text-xs font-semibold mr-3 transition-colors">Log & Close</button>
                            @endif
                            <button wire:click="openEdit({{ $task->id }})" class="text-gaf-blue hover:text-gaf-navy text-xs font-semibold mr-3 transition-colors">Edit</button>
                            @endcan
                            @can('delete', $task)
                            <button wire:click="confirmDelete({{ $task->id }})" class="text-red-500 hover:text-red-700 text-xs font-semibold transition-colors">Delete</button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Card Stack --}}
        <div class="md:hidden divide-y divide-sky-50">
            @foreach($tasks as $task)
            @php
                $pc=['critical'=>'bg-red-100 text-red-700','high'=>'bg-orange-100 text-orange-700','medium'=>'bg-amber-100 text-amber-700','low'=>'bg-green-100 text-green-700'];
                $sc=['completed'=>'bg-green-100 text-green-700','in-progress'=>'bg-blue-100 text-blue-700','pending'=>'bg-gray-100 text-gray-600'];
            @endphp
            <div class="p-4 {{ $task->is_overdue ? 'bg-red-50/30' : '' }}">
                <div class="flex justify-between items-start gap-2 mb-2">
                    <p class="text-sm font-bold text-gaf-navy">{{ $task->title }}</p>
                    <span class="badge {{ $pc[$task->priority] ?? 'bg-gray-100 text-gray-600' }} shrink-0">{{ ucfirst($task->priority) }}</span>
                </div>
                
                <p class="text-xs text-sky-700 font-semibold mb-1">Aircraft: {{ $task->aircraft?->tail_number ?? '—' }}</p>
                <p class="text-xs text-gray-500 mb-3">Due: {{ $task->due_date?->format('d M Y') ?? '—' }} @if($task->is_overdue)<span class="text-red-500 font-bold ml-1">⚠ Overdue</span>@endif</p>
                
                <div class="flex items-center justify-between border-t border-sky-100/50 pt-3">
                    <span class="badge {{ $sc[$task->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst(str_replace('-',' ',$task->status)) }}</span>
                    
                    <div class="flex items-center gap-3">
                        @can('update', $task)
                        @if($task->status !== 'completed')
                        <button wire:click="logAndClose({{ $task->id }})" class="text-green-600 font-bold text-sm px-2 py-1 bg-green-50 rounded-lg">Log & Close</button>
                        @endif
                        <button wire:click="openEdit({{ $task->id }})" class="text-gaf-blue font-bold text-sm px-2 py-1 bg-sky-50 rounded-lg">Edit</button>
                        @endcan
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-5 py-3 border-t border-sky-50 bg-sky-50/50">{{ $tasks->links() }}</div>
        @endif
    </div>
    {{-- Modal --}}
    @if($showModal)
    <div class="modal-overlay" wire:click.self="$set('showModal',false)">
        <div class="modal-panel" @click.stop>
            <div class="modal-header">
                <div>
                    <h2 class="text-base font-bold">{{ $editingId ? 'Edit Task' : 'Create Maintenance Task' }}</h2>
                    <p class="text-xs text-sky-200 mt-0.5">{{ $editingId ? 'Update task details and assignment' : 'Schedule a new maintenance task for an aircraft' }}</p>
                </div>
                <button wire:click="$set('showModal',false)" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="modal-body space-y-5">

                {{-- Section: Task Information --}}
                <fieldset>
                    <legend class="text-[10px] font-bold text-sky-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="inline-block w-4 h-px bg-sky-300"></span> Task Information
                    </legend>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Task Title <span class="text-red-400">*</span></label>
                            <input wire:model="title" type="text" class="gaf-input" placeholder="e.g. 50-hr engine inspection, landing gear hydraulic check"/>
                            @error('title')<p class="mt-1 text-xs text-red-500 flex items-center gap-1">⚠ {{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                            <textarea wire:model="description" rows="3" class="gaf-input resize-none" placeholder="Detailed work order — scope, parts required, special instructions…"></textarea>
                            @error('description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Section: Assignment --}}
                <fieldset>
                    <legend class="text-[10px] font-bold text-sky-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="inline-block w-4 h-px bg-sky-300"></span> Assignment
                    </legend>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Aircraft <span class="text-red-400">*</span></label>
                            <select wire:model="aircraft_id" class="gaf-input">
                                <option value="">— Select Aircraft —</option>
                                @foreach($aircraft as $ac)<option value="{{ $ac->id }}">{{ $ac->tail_number }} — {{ $ac->model }}</option>@endforeach
                            </select>
                            @error('aircraft_id')<p class="mt-1 text-xs text-red-500">⚠ {{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Assigned Engineer</label>
                            <select wire:model="assigned_to" class="gaf-input">
                                <option value="">— Unassigned —</option>
                                @foreach($engineers as $eng)<option value="{{ $eng->id }}">{{ $eng->rank ? $eng->rank.' ' : '' }}{{ $eng->name }}</option>@endforeach
                            </select>
                            @error('assigned_to')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

                {{-- Section: Scheduling & Status --}}
                <fieldset>
                    <legend class="text-[10px] font-bold text-sky-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <span class="inline-block w-4 h-px bg-sky-300"></span> Priority, Status & Scheduling
                    </legend>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Priority <span class="text-red-400">*</span></label>
                            <select wire:model="priority" class="gaf-input">
                                <option value="low">🟢  Low</option>
                                <option value="medium">🟡  Medium</option>
                                <option value="high">🟠  High</option>
                                <option value="critical">🔴  Critical</option>
                            </select>
                            @error('priority')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status <span class="text-red-400">*</span></label>
                            <select wire:model="status" class="gaf-input">
                                <option value="pending">⏳  Pending</option>
                                <option value="in-progress">🔧  In Progress</option>
                                <option value="completed">✅  Completed</option>
                            </select>
                            @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Due Date</label>
                            <input wire:model="due_date" type="date" class="gaf-input"/>
                            @error('due_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </fieldset>

            </div>
            <div class="modal-footer">
                <button wire:click="$set('showModal',false)" class="btn-gaf-outline">Cancel</button>
                <button wire:click="save" wire:loading.attr="disabled" class="btn-gaf">
                    <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Task' : 'Create Task' }}</span>
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
                <h3 class="text-base font-bold text-gaf-navy mb-1">Delete Maintenance Task?</h3>
                <p class="text-sm text-gray-500 mb-6">Any work logs linked to this task will be disassociated. This <strong>cannot be undone</strong>.</p>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteConfirm',false)" class="btn-gaf-outline flex-1">Cancel</button>
                    <button wire:click="deleteTask" wire:loading.attr="disabled" class="btn-danger flex-1">
                        <span wire:loading.remove wire:target="deleteTask">Delete</span>
                        <span wire:loading wire:target="deleteTask">Deleting…</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

