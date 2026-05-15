<div class="relative" x-data="{ open: false }" x-on:click.outside="open=false">

    {{-- Bell button --}}
    <button @click="open=!open"
            class="relative flex items-center justify-center w-9 h-9 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-600 hover:text-gaf-blue transition-all duration-200 border border-sky-100">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($unreadCount > 0)
        <span class="absolute -top-1 -right-1 flex items-center justify-center w-4 h-4 rounded-full bg-red-500 text-white text-[9px] font-bold animate-bounce">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
         class="absolute right-0 top-full mt-2 w-80 bg-white rounded-2xl shadow-gaf-lg border border-sky-100 overflow-hidden z-50"
         style="display:none">

        <div class="flex items-center justify-between px-4 py-3 bg-gaf-gradient">
            <h3 class="text-sm font-bold text-white">Notifications</h3>
            @if($unreadCount > 0)
            <button wire:click="markAllAsRead" class="text-sky-200 hover:text-white text-xs font-medium transition-colors">
                Mark all read
            </button>
            @endif
        </div>

        <div class="max-h-72 overflow-y-auto divide-y divide-sky-50">
            @forelse($notifications as $n)
            <div class="flex items-start gap-3 px-4 py-3 hover:bg-sky-50 transition-colors group">
                <div class="w-8 h-8 rounded-xl bg-sky-100 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-4 h-4 text-gaf-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ $n->data['link'] ?? '#' }}" class="block">
                        <p class="text-xs font-bold text-gray-800 truncate">
                            {{ $n->data['title'] ?? 'System Alert' }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ $n->data['message'] ?? $n->type }}
                        </p>
                    </a>
                    <p class="text-[10px] text-sky-500 mt-1">{{ $n->created_at?->diffForHumans() }}</p>
                </div>
                <button wire:click="markAsRead('{{ $n->id }}')"
                        class="shrink-0 text-sky-300 hover:text-gaf-blue opacity-0 group-hover:opacity-100 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </div>
            @empty
            <div class="py-10 text-center">
                <svg class="w-10 h-10 text-sky-200 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-sm text-gray-400">You're all caught up!</p>
                <p class="text-xs text-sky-400 mt-1">No new notifications</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
