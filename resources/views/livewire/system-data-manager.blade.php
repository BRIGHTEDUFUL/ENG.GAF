<div x-data="{toast:{show:false,type:'success',msg:''},showToast(t,m){this.toast={show:true,type:t,msg:m};setTimeout(()=>this.toast.show=false,3500)}}"
     x-on:notify.window="showToast($event.detail.type,$event.detail.message)" class="animate-fade-in max-w-4xl mx-auto">
    <div x-show="toast.show" x-transition.opacity :class="toast.type==='success'?'bg-green-500':'bg-red-500'"
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-white shadow-gaf text-sm font-semibold" style="display:none">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span x-text="toast.msg"></span>
    </div>

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 mb-6 shadow-gaf">
        <div class="absolute inset-0 opacity-10 grid-bg"></div>
        <div class="relative z-10">
            <span class="inline-block bg-white/10 text-sky-200 text-xs font-semibold px-3 py-1 rounded-full border border-white/20 mb-2">💾 Database Controls</span>
            <h1 class="text-2xl font-black text-white">System Data Backup & Restore</h1>
            <p class="text-sky-200 text-sm mt-1">Export full system snapshots or restore from a previous backup file.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- Export Card --}}
        <div class="gaf-card p-6 flex flex-col justify-between border-t-4 border-t-sky-400">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-gaf-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gaf-navy">Export Snapshot</h2>
                        <p class="text-xs text-gray-500">Download a complete JSON backup</p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 mb-6">
                    This will package all current database records (aircraft, personnel, logs, incidents, wings, and daily states) into a single downloadable JSON snapshot file.
                </p>
            </div>
            
            <button wire:click="exportData" class="btn-gaf w-full justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Generate & Download Backup
            </button>
        </div>

        {{-- Import Card --}}
        <div class="gaf-card p-6 flex flex-col justify-between border-t-4 border-t-amber-400">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gaf-navy">Restore Snapshot</h2>
                        <p class="text-xs text-gray-500">Upload and restore from JSON</p>
                    </div>
                </div>
                
                <p class="text-sm text-gray-600 mb-4">
                    <span class="text-red-500 font-bold">WARNING:</span> This action will wipe all current data and replace it entirely with the contents of the uploaded backup file. Proceed with extreme caution.
                </p>

                <div class="mb-6 relative">
                    <input wire:model="importFile" type="file" accept=".json" class="gaf-input py-2 text-sm cursor-pointer file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-gaf-blue hover:file:bg-sky-100"/>
                    @error('importFile')<p class="mt-1 text-xs text-red-500 flex items-center gap-1">⚠ {{ $message }}</p>@enderror
                </div>
            </div>

            <button wire:click="confirmImport" class="btn-danger w-full justify-center" @if(!$importFile) disabled @endif>
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Restore System Data
            </button>
        </div>
    </div>

    {{-- Restore Confirmation Modal --}}
    @if($showImportConfirm)
    <div class="modal-overlay">
        <div class="relative z-50 w-full max-w-sm bg-white rounded-2xl shadow-gaf-lg border border-red-100 overflow-hidden animate-slide-up">
            <div class="p-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gaf-navy mb-1">CRITICAL WARNING</h3>
                <p class="text-sm text-gray-500 mb-6">You are about to completely overwrite the database. This action <strong>cannot be undone</strong>. Are you absolutely sure?</p>
                <div class="flex gap-3">
                    <button wire:click="$set('showImportConfirm',false)" class="btn-gaf-outline flex-1">Cancel</button>
                    <button wire:click="processImport" wire:loading.attr="disabled" class="btn-danger flex-1">
                        <span wire:loading.remove wire:target="processImport">Yes, Restore</span>
                        <span wire:loading wire:target="processImport" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            Working…
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
