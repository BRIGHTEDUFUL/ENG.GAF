<?php

namespace App\Http\Controllers;

use App\Models\DailyAircraftState;
use Illuminate\Http\Request;

class DailyStateController extends Controller
{
    public function report(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole(['admin', 'commander', 'supervisor', 'auditor'])) {
            abort(403);
        }

        $date   = $request->input('date', now()->toDateString());
        $states = DailyAircraftState::with([
            'aircraft.wing',
            'defects',
            'serviceRemarks',
            'wing',
        ])
        ->forDate($date)
        ->orderBy('wing_id')
        ->orderBy('aircraft_id')
        ->get()
        ->groupBy(fn($s) => $s->wing?->name ?? 'Unassigned');

        return view('daily-state.report', compact('states', 'date'));
    }
}
