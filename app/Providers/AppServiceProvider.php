<?php

namespace App\Providers;

use App\Models\Aircraft;
use App\Models\AuditLog;
use App\Models\FlightLog;
use App\Models\Incident;
use App\Models\MaintenanceLog;
use App\Models\MaintenanceTask;
use App\Models\User;
use App\Models\Wing;
use App\Policies\AircraftPolicy;
use App\Policies\AuditLogPolicy;
use App\Policies\FlightLogPolicy;
use App\Policies\IncidentPolicy;
use App\Policies\MaintenanceLogPolicy;
use App\Policies\MaintenanceTaskPolicy;
use App\Policies\PersonnelPolicy;
use App\Policies\WingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\WingRepositoryInterface::class,
            \App\Repositories\WingRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\AircraftRepositoryInterface::class,
            \App\Repositories\AircraftRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\MaintenanceTaskRepositoryInterface::class,
            \App\Repositories\MaintenanceTaskRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\MaintenanceLogRepositoryInterface::class,
            \App\Repositories\MaintenanceLogRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\FlightLogRepositoryInterface::class,
            \App\Repositories\FlightLogRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\IncidentRepositoryInterface::class,
            \App\Repositories\IncidentRepository::class
        );
    }

    public function boot(): void
    {
        Gate::policy(Wing::class, WingPolicy::class);
        Gate::policy(Aircraft::class, AircraftPolicy::class);
        Gate::policy(MaintenanceTask::class, MaintenanceTaskPolicy::class);
        Gate::policy(MaintenanceLog::class, MaintenanceLogPolicy::class);
        Gate::policy(FlightLog::class, FlightLogPolicy::class);
        Gate::policy(Incident::class, IncidentPolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);
        Gate::policy(User::class, PersonnelPolicy::class); // Personnel Manager uses User model
        Gate::policy('report', \App\Policies\ReportPolicy::class);
    }
}
