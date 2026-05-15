<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AFHQ Daily Aircraft State — {{ \Carbon\Carbon::parse($date)->format('d-M-Y') }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9pt; color: #000; background: #fff; }

        .no-print { display:block; }
        @media print {
            .no-print { display:none !important; }
            @page { size: A4 landscape; margin: 10mm; }
            body { font-size: 8pt; }
            .page-break { page-break-before: always; }
        }

        /* Print button bar */
        .print-bar { background:#0B4FA3; color:#fff; padding:10px 20px; display:flex; align-items:center; justify-content:space-between; gap:10px; }
        .print-bar button { background:#fff; color:#0B4FA3; border:none; padding:6px 16px; border-radius:8px; font-weight:700; font-size:10pt; cursor:pointer; }
        .print-bar button:hover { background:#e0f0ff; }

        /* Report wrapper */
        .report { padding: 8mm 10mm; }

        /* Header */
        .report-title { text-align:center; font-size:13pt; font-weight:bold; text-transform:uppercase; letter-spacing:1px; margin-bottom:2mm; }
        .report-sub   { text-align:center; font-size:10pt; font-weight:bold; text-transform:uppercase; margin-bottom:1mm; }
        .report-meta  { display:flex; justify-content:space-between; font-size:8pt; margin-bottom:3mm; border-top:1px solid #000; border-bottom:1px solid #000; padding:1mm 0; }

        /* Main table */
        table { width:100%; border-collapse:collapse; }
        th, td { border:0.5pt solid #888; padding:1.5mm 2mm; vertical-align:top; }
        th { background:#d0e4f5; font-weight:bold; font-size:7.5pt; text-align:center; white-space:nowrap; }
        td { font-size:7.5pt; }

        /* Wing section row */
        .wing-row td { background:#1a3a6e; color:#fff; font-weight:bold; font-size:8pt; text-transform:uppercase; letter-spacing:0.5px; padding:1.5mm 2mm; }

        /* State badges */
        .state-s   { color:#166534; font-weight:bold; }
        .state-us  { color:#991b1b; font-weight:bold; }
        .state-gnd { color:#374151; font-weight:bold; }

        /* Critical defect */
        .critical { color:#991b1b; font-weight:bold; }

        .defect-list { list-style:none; }
        .defect-list li { margin-bottom:0.5mm; }
        .remark-list { list-style:none; }
        .remark-list li { margin-bottom:0.5mm; }

        /* Footer */
        .report-footer { margin-top:3mm; border-top:0.5pt solid #888; padding-top:1.5mm; display:flex; justify-content:space-between; font-size:7pt; color:#555; }
    </style>
</head>
<body>

{{-- Print bar (hidden on print) --}}
<div class="print-bar no-print">
    <div>
        <strong style="font-size:13pt;">AFHQ Daily Aircraft State</strong>
        <span style="opacity:.7;font-size:10pt;margin-left:8px;">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
    </div>
    <div style="display:flex;gap:8px;">
        <button onclick="window.print()">🖨 Print / Save PDF</button>
        <button onclick="window.history.back()" style="background:#eee;color:#333;">← Back</button>
    </div>
</div>

<div class="report">
    {{-- Title --}}
    <div class="report-title">AFHQ Daily Aircraft and Vehicle State</div>
    <div class="report-sub">Air Force Base — Accra</div>
    <div class="report-meta">
        <span>Report No: {{ \Carbon\Carbon::parse($date)->format('z') + 1 }}</span>
        <span>Date: {{ \Carbon\Carbon::parse($date)->format('d-M-Y') }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:7%">ACT<br>REG/RE</th>
                <th style="width:7%">AIRCRAFT<br>TYPE</th>
                <th style="width:5%">DAILY<br>HRS</th>
                <th style="width:6%">TOTAL<br>HRS</th>
                <th style="width:4%">DAILY<br>LGS</th>
                <th style="width:5%">TOTAL<br>LGS</th>
                <th style="width:4%">STATE</th>
                <th style="width:36%">NATURE OF DEFECT</th>
                <th style="width:26%">NEXT SVC / REMARKS / R.I.E</th>
            </tr>
        </thead>
        <tbody>
            @forelse($states as $wingName => $entries)
            {{-- Wing header row --}}
            <tr class="wing-row">
                <td colspan="9">{{ strtoupper($wingName) }}</td>
            </tr>
            @foreach($entries as $state)
            <tr>
                <td style="font-weight:bold;">{{ $state->aircraft?->tail_number ?? '—' }}</td>
                <td>{{ $state->aircraft?->model ?? '—' }}</td>
                <td style="text-align:center;">
                    {{ $state->daily_flight_hrs > 0 ? number_format($state->daily_flight_hrs, 2) : '—' }}
                </td>
                <td style="text-align:center;">
                    {{ $state->total_flight_hrs ? number_format($state->total_flight_hrs, 2) : '—' }}
                </td>
                <td style="text-align:center;">
                    {{ $state->daily_landings > 0 ? $state->daily_landings : '—' }}
                </td>
                <td style="text-align:center;">
                    {{ $state->total_landings ?? '—' }}
                </td>
                <td style="text-align:center;" class="{{ match($state->state){ 'S'=>'state-s','U/S'=>'state-us',default=>'state-gnd'} }}">
                    {{ $state->display_state }}
                </td>
                <td>
                    @if($state->defects->isNotEmpty())
                    <ul class="defect-list">
                        @foreach($state->defects as $d)
                        <li class="{{ $d->is_critical ? 'critical' : '' }}">
                            {{ $d->roman_numeral }}. {{ $d->description }}
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <span style="color:#999;">—</span>
                    @endif
                </td>
                <td>
                    @if($state->serviceRemarks->isNotEmpty())
                    <ul class="remark-list">
                        @foreach($state->serviceRemarks as $r)
                        <li>
                            {{ $r->roman_numeral }}. {{ $r->description }}
                            @if($r->due_hours) <strong>({{ number_format($r->due_hours,0) }}HRS)</strong>@endif
                            @if($r->service_location) <em>{{ $r->service_location }}</em>@endif
                        </li>
                        @endforeach
                    </ul>
                    @elseif($state->notes)
                    <span>{{ $state->notes }}</span>
                    @else
                    <span style="color:#999;">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
            @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:6mm;color:#999;">No entries for this date.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="report-footer">
        <span>Prepared by: Ghana Air Force Engineering Command System</span>
        <span>Generated: {{ now()->format('d M Y H:i') }}</span>
        <span>CLASSIFIED — For Authorised Personnel Only</span>
    </div>
</div>

</body>
</html>
