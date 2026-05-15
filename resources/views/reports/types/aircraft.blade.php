<x-app-layout>
    <x-slot name="heading">{{ $title }}</x-slot>

    <div class="animate-fade-in">

        {{-- ═══ Report Header ═══ --}}
        <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient p-6 mb-6 shadow-gaf">
            <div class="absolute inset-0 opacity-10 grid-bg"></div>
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1.5 text-sky-300 hover:text-white text-xs font-medium mb-3 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back to Reports
                    </a>
                    <h1 class="text-2xl font-black text-white">{{ $title }}</h1>
                    <p class="text-sky-200 text-sm mt-1">Generated on {{ now()->format('d M Y, H:i') }} · {{ $data->count() }} records</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button onclick="window.print()"
                            class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print Report
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══ Table ═══ --}}
        <div class="gaf-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gaf-navy to-gaf-blue">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Tail #</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Model</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Manufacturer</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Wing</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Year</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Flight Hours</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Last Maint.</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-50">
                        @forelse($data as $row)
                        @php
                            $statusColors = ['active'=>'bg-green-100 text-green-700','maintenance'=>'bg-amber-100 text-amber-700','grounded'=>'bg-red-100 text-red-700','retired'=>'bg-gray-100 text-gray-600'];
                        @endphp
                        <tr class="trow">
                            <td class="px-5 py-3 font-bold text-gaf-navy text-sm">{{ $row->tail_number }}</td>
                            <td class="px-5 py-3 text-sm text-gray-700">{{ $row->model }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $row->manufacturer }}</td>
                            <td class="px-5 py-3 text-sm text-sky-700">{{ $row->wing?->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $row->year_manufactured }}</td>
                            <td class="px-5 py-3 text-sm font-semibold text-gaf-blue">{{ number_format($row->total_flight_hours, 0) }} hrs</td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $row->last_maintenance_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="badge {{ $statusColors[$row->status] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($row->status) }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-gray-400 text-sm">No aircraft data found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 bg-sky-50 border-t border-sky-100 text-xs text-sky-600 font-medium">
                Total: {{ $data->count() }} aircraft · Generated: {{ now()->format('d M Y H:i') }} · Ghana Air Force Engineering Command System
            </div>
        </div>
    </div>
</x-app-layout>
