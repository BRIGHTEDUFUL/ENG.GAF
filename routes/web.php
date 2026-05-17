<?php

use App\Livewire\AircraftManager;
use App\Livewire\AircraftProfile;
use App\Livewire\AuditLogViewer;
use App\Livewire\FlightLogManager;
use App\Livewire\IncidentManager;
use App\Livewire\MaintenanceLogManager;
use App\Livewire\MaintenanceTaskManager;
use App\Livewire\PersonnelManager;
use App\Livewire\DailyStateManager;
use App\Livewire\WingManager;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// -------------------------------------------------------------------------
// Public
// -------------------------------------------------------------------------
Route::get('/', fn() => redirect()->route('dashboard'));

// -------------------------------------------------------------------------
// Authenticated routes
// -------------------------------------------------------------------------
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard — all roles
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Personnel — all authenticated
    Route::get('/personnel', PersonnelManager::class)->name('personnel.index');

    // Wings — admin, commander
    Route::middleware('role:admin,commander')->group(function () {
        Route::get('/wings', WingManager::class)->name('wings.index');
    });

    // Fleet Management — admin, commander, supervisor, auditor
    Route::middleware('role:admin,commander,supervisor,auditor')->group(function () {
        Route::get('/aircraft', AircraftManager::class)->name('aircraft.index');
        Route::get('/aircraft/{id}', AircraftProfile::class)->name('aircraft.profile');
    });

    // Maintenance Tasks — all roles
    Route::get('/maintenance/tasks', MaintenanceTaskManager::class)->name('maintenance.tasks');

    // Maintenance Logs — all roles
    Route::get('/maintenance/logs', MaintenanceLogManager::class)->name('maintenance.logs');

    // Flight Logs — admin, commander, auditor
    Route::middleware('role:admin,commander,auditor')->group(function () {
        Route::get('/flight-logs', FlightLogManager::class)->name('flight-logs.index');
    });

    // Incidents — admin, commander, supervisor
    Route::middleware('role:admin,commander,supervisor')->group(function () {
        Route::get('/incidents', IncidentManager::class)->name('incidents.index');
    });

    // Audit Logs — admin, auditor
    Route::middleware('role:admin,auditor')->group(function () {
        Route::get('/audit-logs', AuditLogViewer::class)->name('audit-logs.index');
    });

    // System Data Manager - admin, commander
    Route::middleware('role:admin,commander')->group(function () {
        Route::get('/system-data', \App\Livewire\SystemDataManager::class)->name('system-data.index');
    });

    // Daily Aircraft State — admin, commander, supervisor, auditor
    Route::middleware('role:admin,commander,supervisor,auditor')->group(function () {
        Route::get('/daily-state', DailyStateManager::class)->name('daily-state.index');
        Route::get('/daily-state/report', [\App\Http\Controllers\DailyStateController::class, 'report'])->name('daily-state.report');
    });

    // Reports — admin, commander, auditor
    Route::get('/reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/generate', [\App\Http\Controllers\ReportController::class, 'generate'])->name('reports.generate');

    // Notifications
    Route::post('/notifications/{id}/read', function (string $id) {
        auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return back();
    })->name('notifications.read');

    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.read-all');
});

require __DIR__.'/auth.php';
