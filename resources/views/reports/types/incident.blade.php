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
                    <p class="text-sky-200 text-sm mt-1">{{ $data->count() }} incidents · {{ now()->format('d M Y, H:i') }}</p>
                </div>
                <button onclick="window.print()" class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>Print
                </button>
            </div>
        </div>

        {{-- Summary pills --}}
        @php
            $byStatus = $data->countBy('status');
            $bySeverity = $data->countBy('severity');
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
            @foreach([['open','Open','bg-red-50 border-red-200 text-red-700'],['under-investigation','Investigating','bg-amber-50 border-amber-200 text-amber-700'],['resolved','Resolved','bg-green-50 border-green-200 text-green-700'],['closed','Closed','bg-gray-50 border-gray-200 text-gray-600']] as [$s,$l,$c])
            <div class="bg-white rounded-2xl border {{ explode(' ',$c)[1] }} p-3 text-center animate-slide-up">
                <p class="text-2xl font-black {{ explode(' ',$c)[2] }}">{{ $byStatus[$s] ?? 0 }}</p>
                <p class="text-xs font-semibold {{ explode(' ',$c)[2] }} mt-0.5">{{ $l }}</p>
            </div>
            @endforeach
        </div>

        <div class="gaf-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gaf-navy to-gaf-blue">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Incident</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Date</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Aircraft</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Reported By</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Investigator</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Severity</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-50">
                        @forelse($data as $row)
                        @php
                            $sc=['critical'=>'bg-red-100 text-red-700','high'=>'bg-orange-100 text-orange-700','medium'=>'bg-amber-100 text-amber-700','low'=>'bg-green-100 text-green-700'];
                            $stc=['open'=>'bg-red-100 text-red-700','under-investigation'=>'bg-blue-100 text-blue-700','resolved'=>'bg-green-100 text-green-700','closed'=>'bg-gray-100 text-gray-600'];
                        @endphp
                        <tr class="trow {{ $row->severity === 'critical' ? 'bg-red-50/30' : '' }}">
                            <td class="px-5 py-3 font-semibold text-gaf-navy text-sm max-w-xs">
                                {{ Str::limit($row->title, 45) }}
                            </td>
                            <td class="px-5 py-3 text-sm text-gray-600 whitespace-nowrap">{{ $row->incident_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm text-sky-700 font-medium">{{ $row->aircraft?->tail_number ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-700">{{ $row->reporter?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $row->investigator?->name ?? 'Unassigned' }}</td>
                            <td class="px-5 py-3"><span class="badge {{ $sc[$row->severity] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($row->severity) }}</span></td>
                            <td class="px-5 py-3"><span class="badge {{ $stc[$row->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst(str_replace('-',' ',$row->status)) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No incidents found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 bg-sky-50 border-t border-sky-100 text-xs text-sky-600 font-medium">
                Total: {{ $data->count() }} incidents · Ghana Air Force Engineering Command System · {{ now()->format('d M Y H:i') }}
            </div>
        </div>
    </div>
</x-app-layout>
