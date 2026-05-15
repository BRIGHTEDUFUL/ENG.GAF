# Design Document: Air Force Engineering Command System

## Overview

A complete military-grade engineering command platform built on Laravel 12, Livewire 3, Blade, Tailwind CSS, Alpine.js, and SQLite/MySQL. The system replaces the existing personnel management app with a 12-module platform covering personnel, wings, aircraft, maintenance, flight operations, incident management, audit logging, notifications, and analytics.

**Roles:** Admin · Commander · Engineer · Supervisor · Auditor

---

## Architecture

### Layer Diagram

```
HTTP Request
    │
    ▼
routes/web.php  (grouped by role middleware)
    │
    ▼
Middleware Stack
  ├── auth
  ├── RoleMiddleware
  ├── AuditMiddleware
  └── LoginThrottleMiddleware
    │
    ▼
Controller (thin — delegates only)
    │
    ▼
Form Request (validate + authorize via Policy)
    │
    ▼
Service Layer (business logic)
    │
    ▼
Repository Layer (Eloquent abstraction)
    │
    ▼
Eloquent Model → SQLite / MySQL
```

### Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                    (Breeze)
│   │   ├── WingController.php
│   │   ├── AircraftController.php
│   │   ├── MaintenanceTaskController.php
│   │   ├── MaintenanceLogController.php
│   │   ├── FlightLogController.php
│   │   ├── IncidentController.php
│   │   ├── AuditLogController.php
│   │   ├── NotificationController.php
│   │   └── ReportController.php
│   ├── Middleware/
│   │   ├── RoleMiddleware.php
│   │   ├── AuditMiddleware.php
│   │   └── LoginThrottleMiddleware.php
│   └── Requests/
│       ├── Wing/StoreWingRequest.php, UpdateWingRequest.php
│       ├── Aircraft/StoreAircraftRequest.php, UpdateAircraftRequest.php
│       ├── MaintenanceTask/StoreMaintenanceTaskRequest.php, UpdateMaintenanceTaskRequest.php
│       ├── MaintenanceLog/StoreMaintenanceLogRequest.php, UpdateMaintenanceLogRequest.php
│       ├── FlightLog/StoreFlightLogRequest.php, UpdateFlightLogRequest.php
│       └── Incident/StoreIncidentRequest.php, UpdateIncidentRequest.php
├── Livewire/
│   ├── Forms/
│   │   ├── WingForm.php
│   │   ├── AircraftForm.php
│   │   ├── MaintenanceTaskForm.php
│   │   ├── MaintenanceLogForm.php
│   │   ├── FlightLogForm.php
│   │   └── IncidentForm.php
│   ├── WingManager.php
│   ├── AircraftManager.php
│   ├── MaintenanceTaskManager.php
│   ├── MaintenanceLogManager.php
│   ├── FlightLogManager.php
│   ├── IncidentManager.php
│   ├── AuditLogViewer.php
│   ├── NotificationPanel.php
│   └── Dashboard.php
├── Models/
│   ├── User.php          (extended)
│   ├── Personnel.php     (extended)
│   ├── Wing.php
│   ├── Aircraft.php
│   ├── MaintenanceTask.php
│   ├── MaintenanceLog.php
│   ├── FlightLog.php
│   ├── Incident.php
│   ├── AuditLog.php
│   └── Notification.php
├── Policies/
│   ├── WingPolicy.php
│   ├── AircraftPolicy.php
│   ├── MaintenanceTaskPolicy.php
│   ├── MaintenanceLogPolicy.php
│   ├── FlightLogPolicy.php
│   ├── IncidentPolicy.php
│   ├── AuditLogPolicy.php
│   └── ReportPolicy.php
├── Repositories/
│   ├── Contracts/
│   │   ├── WingRepositoryInterface.php
│   │   ├── AircraftRepositoryInterface.php
│   │   ├── MaintenanceTaskRepositoryInterface.php
│   │   ├── MaintenanceLogRepositoryInterface.php
│   │   ├── FlightLogRepositoryInterface.php
│   │   └── IncidentRepositoryInterface.php
│   ├── WingRepository.php
│   ├── AircraftRepository.php
│   ├── MaintenanceTaskRepository.php
│   ├── MaintenanceLogRepository.php
│   ├── FlightLogRepository.php
│   └── IncidentRepository.php
└── Services/
    ├── WingService.php
    ├── AircraftService.php
    ├── MaintenanceTaskService.php
    ├── MaintenanceLogService.php
    ├── FlightLogService.php
    ├── IncidentService.php
    ├── AuditService.php
    └── NotificationService.php
```

---

## Database Schema

### `users` (extend existing)

| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| name | VARCHAR(255) | |
| email | VARCHAR(255) UNIQUE | |
| password | VARCHAR(255) | hashed |
| role | ENUM(admin,commander,engineer,supervisor,auditor) | default viewer→engineer |
| rank | VARCHAR(50) | nullable |
| wing_id | FK → wings | nullable |
| failed_attempts | INT | default 0 |
| last_failed_login | TIMESTAMP | nullable |
| remember_token | VARCHAR(100) | |
| email_verified_at | TIMESTAMP | nullable |
| created_at / updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | soft delete |

### `wings`

| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| name | VARCHAR(150) UNIQUE | |
| code | VARCHAR(20) UNIQUE | |
| base_location | VARCHAR(255) | |
| commander_id | FK → users | nullable |
| status | ENUM(active,inactive) | default active |
| established_date | DATE | nullable |
| description | TEXT | nullable |
| created_at / updated_at | TIMESTAMP | |

### `aircraft`

| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| tail_number | VARCHAR(20) UNIQUE | |
| model | VARCHAR(100) | |
| manufacturer | VARCHAR(100) | |
| year_manufactured | INT | nullable |
| wing_id | FK → wings | nullable, indexed |
| status | ENUM(active,maintenance,grounded,retired) | default active |
| last_maintenance_date | DATE | nullable |
| total_flight_hours | DECIMAL(10,2) | default 0 |
| notes | TEXT | nullable |
| created_at / updated_at | TIMESTAMP | |

### `maintenance_tasks`

| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| title | VARCHAR(200) | |
| description | TEXT | nullable |
| aircraft_id | FK → aircraft | indexed |
| assigned_to | FK → users | nullable, indexed |
| created_by | FK → users | indexed |
| priority | ENUM(low,medium,high,critical) | default medium |
| status | ENUM(pending,in-progress,completed) | default pending |
| due_date | DATE | nullable |
| completed_at | TIMESTAMP | nullable |
| created_at / updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | soft delete |

### `maintenance_logs`

| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| maintenance_task_id | FK → maintenance_tasks | nullable, indexed |
| aircraft_id | FK → aircraft | indexed |
| engineer_id | FK → users | indexed |
| work_performed | TEXT | |
| parts_used | TEXT | nullable |
| hours_spent | DECIMAL(6,2) | |
| log_date | DATE | |
| status | ENUM(draft,submitted,approved) | default draft |
| approved_by | FK → users | nullable |
| approved_at | TIMESTAMP | nullable |
| created_at / updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | soft delete |

### `flight_logs`

| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| aircraft_id | FK → aircraft | indexed |
| pilot_id | FK → users | indexed |
| co_pilot_id | FK → users | nullable |
| departure_location | VARCHAR(255) | |
| arrival_location | VARCHAR(255) | |
| departure_time | TIMESTAMP | |
| arrival_time | TIMESTAMP | |
| flight_duration_minutes | INT | computed on save |
| max_altitude_ft | INT | nullable |
| max_speed_knots | INT | nullable |
| gps_track | JSON | nullable |
| mission_type | ENUM(training,operational,test,ferry) | |
| notes | TEXT | nullable |
| created_at / updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | soft delete |

### `incidents`

| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| title | VARCHAR(200) | |
| description | TEXT | |
| aircraft_id | FK → aircraft | nullable, indexed |
| reported_by | FK → users | indexed |
| assigned_investigator_id | FK → users | nullable |
| severity | ENUM(low,medium,high,critical) | |
| status | ENUM(open,under-investigation,resolved,closed) | default open |
| incident_date | TIMESTAMP | |
| resolution_notes | TEXT | nullable |
| resolved_at | TIMESTAMP | nullable |
| created_at / updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | soft delete |

### `audit_logs`

| Column | Type | Notes |
|---|---|---|
| id | BIGINT PK | |
| user_id | FK → users | nullable, indexed |
| event | VARCHAR(100) | indexed |
| auditable_type | VARCHAR(255) | nullable |
| auditable_id | BIGINT | nullable |
| old_values | JSON | nullable |
| new_values | JSON | nullable |
| ip_address | VARCHAR(45) | nullable |
| user_agent | VARCHAR(500) | nullable |
| created_at | TIMESTAMP | NO updated_at — immutable |

### `notifications` (Laravel built-in)

| Column | Type | Notes |
|---|---|---|
| id | UUID PK | |
| type | VARCHAR(255) | |
| notifiable_type | VARCHAR(255) | |
| notifiable_id | BIGINT | |
| data | TEXT/JSON | |
| read_at | TIMESTAMP | nullable |
| created_at / updated_at | TIMESTAMP | |

---

## Eloquent Relationships

```
User
  belongsTo Wing (wing_id)
  hasMany MaintenanceLog (engineer_id)
  hasMany MaintenanceTask (assigned_to)
  hasMany FlightLog (pilot_id)
  hasMany Incident (reported_by)
  hasMany Notification (notifiable)

Wing
  belongsTo User (commander_id)
  hasMany Aircraft (wing_id)
  hasMany User (wing_id)

Aircraft
  belongsTo Wing (wing_id)
  hasMany MaintenanceTask (aircraft_id)
  hasMany MaintenanceLog (aircraft_id)
  hasMany FlightLog (aircraft_id)
  hasMany Incident (aircraft_id)

MaintenanceTask
  belongsTo Aircraft
  belongsTo User (assigned_to)
  belongsTo User (created_by)
  hasMany MaintenanceLog (maintenance_task_id)

MaintenanceLog
  belongsTo Aircraft
  belongsTo User (engineer_id)
  belongsTo MaintenanceTask (nullable)
  belongsTo User (approved_by, nullable)

FlightLog
  belongsTo Aircraft
  belongsTo User (pilot_id)
  belongsTo User (co_pilot_id, nullable)

Incident
  belongsTo Aircraft (nullable)
  belongsTo User (reported_by)
  belongsTo User (assigned_investigator_id, nullable)

AuditLog
  belongsTo User (nullable)
  morphTo auditable (auditable_type + auditable_id)
```

---

## Middleware Stack

### `RoleMiddleware`
```php
// Usage: middleware('role:admin,commander')
// Checks Auth::user()->role is in the allowed list
// On failure: abort(403) + AuditService::logPolicyDenied()
```

### `AuditMiddleware`
```php
// Applied to all authenticated routes
// Records page access events to audit_logs
// Captures IP address and user agent
```

### `LoginThrottleMiddleware`
```php
// Applied to POST /login
// Checks failed_attempts >= 5 within 15 minutes
// Returns 429 with lockout message if exceeded
```

---

## Service Interfaces

### `WingService`
- `list(array $filters): LengthAwarePaginator`
- `find(int $id): Wing`
- `create(array $data): Wing`
- `update(Wing $wing, array $data): Wing`
- `delete(Wing $wing): bool`
- `assignCommander(Wing $wing, User $commander): Wing`

### `AircraftService`
- `list(array $filters): LengthAwarePaginator`
- `find(int $id): Aircraft`
- `create(array $data): Aircraft`
- `update(Aircraft $aircraft, array $data): Aircraft`
- `updateStatus(Aircraft $aircraft, string $status, User $actor): Aircraft`
- `delete(Aircraft $aircraft): bool`

### `MaintenanceTaskService`
- `list(array $filters): LengthAwarePaginator`
- `find(int $id): MaintenanceTask`
- `create(array $data, User $creator): MaintenanceTask`
- `assign(MaintenanceTask $task, User $engineer): MaintenanceTask`
- `updateStatus(MaintenanceTask $task, string $status): MaintenanceTask`
- `delete(MaintenanceTask $task): bool`

### `MaintenanceLogService`
- `list(array $filters): LengthAwarePaginator`
- `create(array $data, User $engineer): MaintenanceLog`
- `approve(MaintenanceLog $log, User $supervisor): MaintenanceLog`
- `update(MaintenanceLog $log, array $data): MaintenanceLog`

### `FlightLogService`
- `list(array $filters): LengthAwarePaginator`
- `create(array $data): FlightLog`
- `find(int $id): FlightLog`
- `delete(FlightLog $log): bool`

### `IncidentService`
- `list(array $filters): LengthAwarePaginator`
- `create(array $data, User $reporter): Incident`
- `assignInvestigator(Incident $incident, User $investigator): Incident`
- `resolve(Incident $incident, string $notes, User $resolver): Incident`
- `updateStatus(Incident $incident, string $status): Incident`

### `AuditService`
- `log(string $event, ?User $user, ?Model $auditable, array $old, array $new): void`
- `logAuth(string $event, User $user, Request $request): void`
- `logPolicyDenied(User $user, string $action, string $model): void`

### `NotificationService`
- `notifyTaskAssigned(MaintenanceTask $task): void`
- `notifyTaskOverdue(MaintenanceTask $task): void`
- `notifyCriticalIncident(Incident $incident): void`
- `notifyLogPendingApproval(MaintenanceLog $log): void`
- `markRead(Notification $notification): void`
- `markAllRead(User $user): void`

---

## Livewire Components

| Component | Purpose |
|---|---|
| `Dashboard` | Role-scoped stats cards + charts, polls every 60s |
| `WingManager` | Wing CRUD list + modal |
| `AircraftManager` | Aircraft CRUD list + modal + status change |
| `MaintenanceTaskManager` | Task list + modal + real-time status updates |
| `MaintenanceLogManager` | Log list + modal + approval workflow |
| `FlightLogManager` | Flight log list + modal |
| `IncidentManager` | Incident list + modal + investigation workflow |
| `AuditLogViewer` | Paginated audit log with filters |
| `NotificationPanel` | Dropdown panel, polls every 30s, mark read |
| `PersonnelManager` | Extended from existing — adds rank, wing, hours |

---

## UI Design System

### Color Palette
```
Background:    #0F172A  (slate-950 / dark navy)
Surface:       #1E293B  (slate-800)
Surface-2:     #334155  (slate-700)
Border:        #475569  (slate-600)
Text primary:  #F1F5F9  (slate-100)
Text muted:    #94A3B8  (slate-400)
Accent blue:   #3B82F6  (blue-500)
Accent hover:  #2563EB  (blue-600)
```

### Status Badge Colors
```
active / completed / resolved / approved  → green-500
pending / open / draft                    → yellow-500
in-progress / under-investigation         → blue-500
grounded / critical / retired             → red-500
maintenance / high                        → orange-500
inactive / low                            → slate-400
```

### Layout
- Fixed sidebar: 256px wide, `#1E293B` background, white icons + text
- Top nav bar: `#1E293B`, 64px height, user info + notification bell
- Main content: `#0F172A` background, padded 24px
- Cards: `#1E293B` background, `#334155` border, rounded-xl

---

## Blade Views

```
resources/views/
├── layouts/
│   ├── app.blade.php          (dark sidebar layout)
│   ├── guest.blade.php        (auth pages)
│   └── navigation.blade.php   (sidebar partial)
├── dashboard.blade.php
├── livewire/
│   ├── wing-manager.blade.php
│   ├── aircraft-manager.blade.php
│   ├── maintenance-task-manager.blade.php
│   ├── maintenance-log-manager.blade.php
│   ├── flight-log-manager.blade.php
│   ├── incident-manager.blade.php
│   ├── audit-log-viewer.blade.php
│   ├── notification-panel.blade.php
│   └── personnel-manager.blade.php
└── profile/
    ├── edit.blade.php
    └── partials/
        ├── update-profile-information-form.blade.php
        ├── update-password-form.blade.php
        └── delete-user-form.blade.php
```

---

## Routes

```php
// routes/web.php

// Auth (Breeze)
require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // Personnel
    Route::get('/personnel', PersonnelManager::class)->name('personnel.index');

    // Wings — admin, commander
    Route::middleware('role:admin,commander')->group(function () {
        Route::get('/wings', WingManager::class)->name('wings.index');
    });

    // Aircraft — admin, commander, supervisor, auditor
    Route::middleware('role:admin,commander,supervisor,auditor')->group(function () {
        Route::get('/aircraft', AircraftManager::class)->name('aircraft.index');
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

    // Reports — admin, commander, auditor
    Route::middleware('role:admin,commander,auditor')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // Notifications
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});
```

---

## Correctness Properties

1. **Failed login lockout** — After 5 failed attempts within 15 minutes, `failed_attempts >= 5` AND login is rejected.
2. **Role gate enforcement** — For any user with role NOT in the allowed list, every protected route returns 403.
3. **Maintenance log hours accumulate** — `personnel.total_hours_logged` equals the sum of all `maintenance_logs.hours_spent` for that engineer.
4. **Aircraft flight hours accumulate** — `aircraft.total_flight_hours` equals the sum of all `flight_logs.flight_duration_minutes / 60` for that aircraft.
5. **Flight log duration computed** — `flight_duration_minutes` always equals `(arrival_time - departure_time)` in minutes; arrival must be after departure.
6. **Approved log immutability** — A `MaintenanceLog` with `status = approved` cannot be updated by an Engineer.
7. **Commander uniqueness** — No user with role `commander` can be assigned as commander to more than one Wing simultaneously.
8. **Incident resolution requires notes** — An Incident cannot transition to `resolved` status without non-empty `resolution_notes`.
9. **Audit log immutability** — No user can update or delete any `AuditLog` record; count only ever increases.
10. **Soft-delete exclusion** — Soft-deleted records never appear in list, search, or filter results by default.
11. **Critical incident notification** — Every Incident with `severity = critical` triggers notifications to all users with `admin` or `commander` role.
12. **Engineer assignment validation** — A `MaintenanceTask` can only be assigned to a user whose `role = engineer`.

---

## Error Handling

- `ModelNotFoundException` → 404 response
- `AuthorizationException` → 403 response + AuditLog entry
- `ValidationException` → inline field errors via Livewire
- Database transaction failures → rollback + error notification
- Queue failures → logged to `failed_jobs` table

---

## Testing Strategy

- **Unit tests** — Service classes and Repository classes in isolation using SQLite in-memory
- **Feature tests** — Livewire component tests using `Livewire::test()` for each manager component
- **Policy tests** — Each Policy method tested for all 5 roles
- **Property-based tests** — Using `eris/eris` for the 12 correctness properties above (100 iterations each)
- **Auth tests** — Breeze-generated tests extended with lockout and role tests
