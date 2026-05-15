<x-app-layout>
    <x-slot name="heading">{{ $title }}</x-slot>
    <div class="animate-fade-in">
        <div class="relative overflow-hidden rounded-3xl bg-gaf-gradient print:bg-white print:text-black print:shadow-none p-6 mb-6 shadow-gaf">
            <div class="absolute inset-0 opacity-10 grid-bg print:hidden"></div>
            
            {{-- Print Logo (Only visible when printing) --}}
            <div class="hidden print:flex items-center gap-4 mb-4 border-b pb-4 border-gray-200">
                <img src="{{ asset('images/gaf-logo.png') }}" alt="GAF Logo" class="w-16 h-16 object-contain">
                <div>
                    <h2 class="text-xl font-bold text-black">GHANA AIR FORCE</h2>
                    <p class="text-sm font-semibold text-gray-600">ENGINEERING COMMAND REPORT</p>
                </div>
            </div>

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1.5 text-sky-300 hover:text-white print:hidden text-xs font-medium mb-3 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Back to Reports
                    </a>
                    <h1 class="text-2xl font-black text-white print:text-black">{{ $title }}</h1>
                    <p class="text-sky-200 print:text-gray-600 text-sm mt-1">{{ $data->count() }} records · {{ now()->format('d M Y, H:i') }}</p>
                </div>
                <button onclick="window.print()" class="print:hidden flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>Print
                </button>
            </div>
        </div>
        <div class="gaf-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gaf-navy to-gaf-blue">
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">#</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Name</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Email</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Rank</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Role</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Wing</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-50">
                        @forelse($data as $i => $row)
                        @php
                            $roleC=['admin'=>'bg-purple-100 text-purple-700','commander'=>'bg-blue-100 text-blue-700',
                                    'supervisor'=>'bg-amber-100 text-amber-700','engineer'=>'bg-green-100 text-green-700','auditor'=>'bg-sky-100 text-sky-700'];
                        @endphp
                        <tr class="trow {{ $row->deleted_at ? 'opacity-50' : '' }}">
                            <td class="px-5 py-3 text-xs text-gray-400 font-mono">{{ $i+1 }}</td>
                            <td class="px-5 py-3 font-semibold text-gaf-navy text-sm">{{ $row->name }}</td>
                            <td class="px-5 py-3 text-sm text-sky-700">{{ $row->email }}</td>
                            <td class="px-5 py-3 text-sm text-gray-700">{{ $row->rank ?? '—' }}</td>
                            <td class="px-5 py-3"><span class="badge {{ $roleC[$row->role] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($row->role) }}</span></td>
                            <td class="px-5 py-3 text-sm text-gray-600">{{ $row->wing?->name ?? 'Unassigned' }}</td>
                            <td class="px-5 py-3">
                                @if($row->deleted_at)
                                    <span class="badge bg-red-100 text-red-700">Deactivated</span>
                                @else
                                    <span class="badge bg-green-100 text-green-700">Active</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No personnel found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 bg-sky-50 border-t border-sky-100 text-xs text-sky-600 font-medium">
                Total: {{ $data->count() }} personnel · Ghana Air Force Engineering Command System · {{ now()->format('d M Y H:i') }}
            </div>
        </div>
    </div>
</x-app-layout>
