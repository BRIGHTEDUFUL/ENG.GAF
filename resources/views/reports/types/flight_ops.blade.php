<x-app-layout>
    <x-slot name="heading">{{ $title }}</x-slot>
    <div class="animate-fade-in">
        <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 mb-6 shadow-gaf">
            <div class="absolute inset-0 opacity-10 grid-bg"></div>
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1.5 text-sky-300 hover:text-white text-xs font-medium mb-3 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Back to Reports
                    </a>
                    <h1 class="text-2xl font-black text-white">{{ $title }}</h1>
                    <p class="text-sky-200 text-sm mt-1">{{ $data->count() }} flight records · {{ now()->format('d M Y, H:i') }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="bg-white/10 border border-white/20 rounded-2xl px-4 py-3 text-center">
                        <p class="text-xl font-black text-white">{{ $data->sum('flight_duration_minutes') ? round($data->sum('flight_duration_minutes')/60, 1) : 0 }}</p>
                        <p class="text-sky-300 text-xs">Total Hours</p>
                    </div>
                    <button onclick="window.print()" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>Print
                    </button>
                </div>
            </div>
        </div>

        <div class="gaf-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gaf-navy to-gaf-blue">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Log #</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Aircraft</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Pilot</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Mission Type</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Departure</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Arrival</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Duration</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Route</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-50">
                        @forelse($data as $row)
                        <tr class="trow">
                            <td class="px-5 py-3 text-xs font-mono text-gray-400">#{{ $row->id }}</td>
                            <td class="px-5 py-3 text-sm font-semibold text-sky-700">{{ $row->aircraft?->tail_number ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm text-gaf-navy font-medium">{{ $row->pilot?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ ucfirst($row->mission_type ?? '—') }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $row->departure_time?->format('d M H:i') ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $row->arrival_time?->format('d M H:i') ?? 'In progress' }}</td>
                            <td class="px-5 py-3 text-sm font-semibold text-gaf-blue">
                                {{ $row->flight_duration_minutes ? round($row->flight_duration_minutes/60,1).' hrs' : '—' }}
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500">
                                {{ $row->departure_location ?? '—' }} → {{ $row->arrival_location ?? '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400 text-sm">No flight logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 bg-sky-50 border-t border-sky-100 text-xs text-sky-600 font-medium">
                {{ $data->count() }} flights · Total {{ round($data->sum('flight_duration_minutes')/60,1) }} hrs · Ghana Air Force Engineering Command System · {{ now()->format('d M Y H:i') }}
            </div>
        </div>
    </div>
</x-app-layout>
