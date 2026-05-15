<div class="animate-fade-in">

    {{-- Page Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 mb-6 shadow-gaf">
        <div class="absolute inset-0 opacity-10 grid-bg"></div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-block bg-white/10 text-sky-200 text-xs font-semibold px-3 py-1 rounded-full border border-white/20 mb-2">🔒 Security & Compliance</span>
                <h1 class="text-2xl font-black text-white">Audit Logs</h1>
                <p class="text-sky-200 text-sm mt-1">Immutable record of system events and user actions</p>
            </div>
            <div class="flex items-center gap-2 bg-white/10 border border-white/20 rounded-2xl px-4 py-3">
                <svg class="w-5 h-5 text-sky-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-white text-sm font-semibold">Tamper-Proof</span>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="gaf-card p-4 mb-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <input wire:model.live.debounce.300ms="userFilter" type="text" placeholder="Filter by user…" class="gaf-input pl-10"/>
            </div>
            <input wire:model.live="dateFilter" type="date" class="gaf-input sm:w-44"/>
            <select wire:model.live="eventFilter" class="gaf-input sm:w-44">
                <option value="">All Events</option>
                <option value="login">Login</option>
                <option value="logout">Logout</option>
                <option value="failed_login">Failed Login</option>
                <option value="page_access">Page Access</option>
                <option value="created">Created</option>
                <option value="updated">Updated</option>
                <option value="deleted">Deleted</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="gaf-card overflow-hidden">
        @if($logs->isEmpty())
        <div class="py-16 text-center">
            <svg class="w-12 h-12 text-sky-200 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            <p class="text-gray-400 text-sm font-medium">No audit logs found</p>
            <p class="text-xs text-sky-400 mt-1">Adjust filters to see results</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gaf-navy to-gaf-blue">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Timestamp</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">User</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Event</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden lg:table-cell">Resource</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider hidden xl:table-cell">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-50">
                    @foreach($logs as $log)
                    @php
                        $eventColors = [
                            'login'        => 'bg-green-100 text-green-700',
                            'logout'       => 'bg-gray-100 text-gray-600',
                            'failed_login' => 'bg-red-100 text-red-700',
                            'created'      => 'bg-blue-100 text-blue-700',
                            'updated'      => 'bg-amber-100 text-amber-700',
                            'deleted'      => 'bg-red-100 text-red-700',
                            'page_access'  => 'bg-sky-100 text-sky-700',
                        ];
                        $ec = $eventColors[$log->event] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <tr wire:key="log-{{ $log->id }}" class="trow">
                        <td class="px-5 py-3 text-xs font-mono text-gray-500 whitespace-nowrap">
                            {{ $log->created_at?->format('d M Y H:i:s') }}
                        </td>
                        <td class="px-5 py-3">
                            <p class="text-sm font-semibold text-gaf-navy">{{ $log->user?->name ?? 'System' }}</p>
                            @if($log->user)
                            <p class="text-xs text-sky-500">{{ $log->user->email }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="badge {{ $ec }}">{{ strtoupper(str_replace('_',' ',$log->event)) }}</span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500 hidden lg:table-cell">
                            @if($log->auditable_type)
                            <span class="font-semibold text-gaf-navy">{{ class_basename($log->auditable_type) }}</span>
                            <span class="text-sky-500"> #{{ $log->auditable_id }}</span>
                            @elseif(isset($log->new_values['url']))
                            <span class="font-mono">{{ $log->new_values['url'] }}</span>
                            @else
                            —
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs font-mono text-gray-500 hidden xl:table-cell">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-sky-50 bg-sky-50/50">{{ $logs->links() }}</div>
        @endif
    </div>
</div>
