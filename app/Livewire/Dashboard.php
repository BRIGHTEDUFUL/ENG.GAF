<?php

namespace App\Livewire;

use App\Models\Aircraft;
use App\Models\DailyAircraftState;
use App\Models\DailyDefect;
use App\Models\MaintenanceTask;
use App\Models\Incident;
use App\Models\Wing;
use App\Models\Personnel;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $totalAircraft    = Aircraft::count();
        $activeAircraft   = Aircraft::where('status','active')->count();
        $maintenanceCount = Aircraft::where('status','maintenance')->count();
        $groundedCount    = Aircraft::where('status','grounded')->count();
        $openTasks        = MaintenanceTask::where('status','!=','completed')->count();
        $criticalTasks    = MaintenanceTask::where('priority','critical')->where('status','!=','completed')->count();
        $openIncidents    = Incident::where('status','open')->count();
        $criticalIncidents= Incident::where('severity','critical')->whereIn('status',['open','under-investigation'])->count();
        $totalPersonnel   = Personnel::count();
        $activeWings      = Wing::where('status','active')->count();

        $recentTasks = MaintenanceTask::with(['aircraft','assignedEngineer'])->latest()->take(6)->get();

        $statuses = ['active'=>['label'=>'Active','color'=>'bg-green-500'],
                     'maintenance'=>['label'=>'Maintenance','color'=>'bg-yellow-500'],
                     'grounded'=>['label'=>'Grounded','color'=>'bg-red-500'],
                     'retired'=>['label'=>'Retired','color'=>'bg-slate-600']];
        $total = max($totalAircraft, 1);
        $statusBreakdown = [];
        foreach($statuses as $status => $meta) {
            $count = Aircraft::where('status',$status)->count();
            $statusBreakdown[] = [
                'label' => $meta['label'],
                'color' => $meta['color'],
                'count' => $count,
                'pct'   => round($count/$total*100)
            ];
        }

        $recentIncidents = Incident::latest()->take(3)->get();

        // Daily State stats
        $dsToday       = now()->toDateString();
        $dsServiceable = DailyAircraftState::forDate($dsToday)->where('state','S')->count();
        $dsUnservice   = DailyAircraftState::forDate($dsToday)->where('state','U/S')->count();
        $dsTotal       = DailyAircraftState::forDate($dsToday)->count();
        $dsCritical    = DailyDefect::whereHas('dailyAircraftState', fn($q) => $q->forDate($dsToday))
                            ->where('is_critical', true)->count();
        $dsHasData     = $dsTotal > 0;

        return view('livewire.dashboard', compact(
            'totalAircraft', 'activeAircraft', 'maintenanceCount', 'groundedCount',
            'openTasks', 'criticalTasks', 'openIncidents', 'criticalIncidents',
            'totalPersonnel', 'activeWings', 'recentTasks', 'statusBreakdown', 'recentIncidents',
            'dsServiceable', 'dsUnservice', 'dsTotal', 'dsCritical', 'dsHasData', 'dsToday'
        ))->layout('layouts.app', ['heading' => 'Command Dashboard']);
    }
}
