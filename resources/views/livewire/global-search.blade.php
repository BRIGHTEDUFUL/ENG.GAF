<div x-data="{
        isOpen: @entangle('isOpen'),
        open() { this.isOpen = true; setTimeout(() => $refs.searchInput.focus(), 50); },
        close() { this.isOpen = false; }
    }"
    @keydown.window.prevent.cmd.k="open"
    @keydown.window.prevent.ctrl.k="open"
    @keydown.window.escape="close">

    {{-- Invisible trigger just in case --}}
    <button @click="open" class="hidden" id="global-search-trigger"></button>

    {{-- Search Button (For Navbar) --}}
    <button @click="open" class="flex items-center gap-2 bg-sky-50 hover:bg-sky-100 border border-sky-100 text-sky-700 px-3 py-1.5 rounded-xl transition-colors text-sm font-medium w-full sm:w-auto">
        <svg class="w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <span class="hidden sm:inline">Quick Search...</span>
        <span class="hidden sm:inline text-sky-400 text-xs bg-sky-100 px-1.5 py-0.5 rounded ml-2">⌘K</span>
    </button>

    {{-- Modal --}}
    <div x-show="isOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-start justify-center pt-16 sm:pt-24 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div x-show="isOpen" x-transition.opacity class="fixed inset-0 bg-gaf-navy/80 backdrop-blur-sm transition-opacity" @click="close"></div>

        <div x-show="isOpen"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle w-full max-w-2xl border border-sky-100">
            
            <div class="relative">
                <div class="flex items-center px-4 border-b border-sky-50">
                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-ref="searchInput" wire:model.live.debounce.300ms="query" 
                           class="w-full border-0 focus:ring-0 text-gray-800 placeholder-gray-400 py-4 px-3 text-base" 
                           placeholder="Search aircraft tail numbers, tasks, personnel...">
                    
                    <button @click="close" class="text-gray-400 hover:text-gray-600 px-2">
                        <span class="text-[10px] font-bold bg-gray-100 px-1.5 py-0.5 rounded">ESC</span>
                    </button>
                </div>

                <div class="max-h-[60vh] overflow-y-auto p-2 bg-slate-50/50">
                    @if(strlen($query) >= 2)
                        @if($results->isEmpty())
                            <div class="py-12 text-center">
                                <p class="text-sm text-gray-500">No results found for "<span class="font-bold text-gray-800">{{ $query }}</span>"</p>
                            </div>
                        @else
                            <div class="space-y-1">
                                @foreach($results as $res)
                                <a href="{{ $res['url'] }}" wire:navigate class="flex items-center gap-3 p-3 rounded-xl hover:bg-sky-50 transition-colors group">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 {{ $res['color'] }}">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">{!! $res['icon'] !!}</svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate group-hover:text-gaf-blue">{{ $res['title'] }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $res['desc'] }}</p>
                                    </div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 shrink-0">{{ $res['type'] }}</span>
                                </a>
                                @endforeach
                            </div>
                        @endif
                    @elseif(strlen($query) > 0)
                        <div class="py-8 text-center">
                            <p class="text-sm text-gray-400">Type at least 2 characters...</p>
                        </div>
                    @else
                        <div class="py-8 text-center flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-200 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-600 mb-1">Quick Actions</p>
                            <p class="text-xs text-gray-400 max-w-[250px]">Search for any tail number, engineer's name, or maintenance task directly.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
