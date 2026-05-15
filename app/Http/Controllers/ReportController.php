<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Aircraft;
use App\Models\MaintenanceTask;
use App\Models\User;
use App\Models\Incident;
use App\Models\FlightLog;

class ReportController extends Controller
{
    /**
     * Only admin, commander and auditor can access reports.
     */
    private function authorizeReport(): void
    {
        $user = auth()->user();
        if (! $user || ! $user->hasRole(['admin', 'commander', 'auditor'])) {
            abort(403, 'Access denied. Reports are restricted to Admin, Commander, and Auditor roles.');
        }
    }

    public function index()
    {
        $this->authorizeReport();
        return view('reports.index');
    }

    public function generate(Request $request)
    {
        $this->authorizeReport();

        $type = $request->input('type');

        $reports = [
            'aircraft'   => ['title' => 'Aircraft Fleet Report',       'view' => 'reports.types.aircraft'],
            'maintenance'=> ['title' => 'Maintenance Tasks Report',     'view' => 'reports.types.maintenance'],
            'personnel'  => ['title' => 'Personnel Roster',            'view' => 'reports.types.personnel'],
            'incident'   => ['title' => 'Incident Report',             'view' => 'reports.types.incident'],
            'flight_ops' => ['title' => 'Flight Operations Report',    'view' => 'reports.types.flight_ops'],
        ];

        if (! isset($reports[$type])) {
            abort(404, 'Report type not found.');
        }

        $title = $reports[$type]['title'];

        $data = match($type) {
            'aircraft'    => Aircraft::with('wing')->orderBy('tail_number')->get(),
            'maintenance' => MaintenanceTask::with(['aircraft', 'assignedEngineer', 'createdBy'])->latest()->get(),
            'personnel'   => User::with('wing')->withTrashed()->orderBy('name')->get(),
            'incident'    => Incident::with(['aircraft', 'reporter', 'investigator'])->latest('incident_date')->get(),
            'flight_ops'  => FlightLog::with(['aircraft', 'pilot'])->latest('departure_time')->get(),
        };

        return view($reports[$type]['view'], compact('data', 'title', 'type'));
    }
}
