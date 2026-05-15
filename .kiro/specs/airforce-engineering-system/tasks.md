# Implementation Plan: Air Force Engineering Command System

## Overview

Complete rebuild of the existing personnel management app into a 12-module military-grade engineering command platform. Tasks are ordered so each layer is complete before the next depends on it.

## Tasks

- [x] 1. Database migrations and base models
  - [x] 1.1 Extend users table: add rank, wing_id, failed_attempts, last_failed_login, soft_deletes; update User model
  - [x] 1.2 Create wings migration and Wing model with relationships
  - [x] 1.3 Create aircraft migration and Aircraft model with relationships
  - [x] 1.4 Create maintenance_tasks migration and MaintenanceTask model
  - [x] 1.5 Create maintenance_logs migration and MaintenanceLog model
  - [x] 1.6 Create flight_logs migration and FlightLog model
  - [x] 1.7 Create incidents migration and Incident model
  - [x] 1.8 Create audit_logs migration and AuditLog model (no updated_at, immutable)
  - [x] 1.9 Run php artisan notifications:table and migrate all
  - [x] 1.10 Create all factories and seeders (admin, commander, engineer, supervisor, auditor users + sample data)

- [x] 2. Authentication and RBAC
  - [x] 2.1 Update RoleMiddleware to support 5 roles (admin, commander, engineer, supervisor, auditor)
  - [x] 2.2 Create LoginThrottleMiddleware (5 attempts / 15 min lockout, updates failed_attempts)
  - [x] 2.3 Create AuditMiddleware (logs page access with IP + user agent)
  - [x] 2.4 Register all middleware in bootstrap/app.php
  - [x] 2.5 Update all route groups with correct role middleware
  - [x] 2.6 Create AuditService with log(), logAuth(), logPolicyDenied() methods
  - [x] 2.7 Hook AuditService into login/logout/failed-login events via EventServiceProvider

- [x] 3. Service and Repository layer scaffolding
  - [x] 3.1 Create Repository interfaces and concrete classes for all 6 modules
  - [x] 3.2 Create Service classes for all 8 services (Wing, Aircraft, MaintenanceTask, MaintenanceLog, FlightLog, Incident, Audit, Notification)
  - [x] 3.3 Bind interfaces to implementations in AppServiceProvider

- [x] 4. Policies
  - [x] 4.1 Create WingPolicy (admin full, commander create/update/read, others read)
  - [x] 4.2 Create AircraftPolicy (admin full, commander create/update/read, others read)
  - [x] 4.3 Create MaintenanceTaskPolicy (admin/supervisor full, engineer update own, auditor read)
  - [x] 4.4 Create MaintenanceLogPolicy (admin full, engineer create/update own draft, supervisor approve, auditor read)
  - [x] 4.5 Create FlightLogPolicy (admin/commander full, others read)
  - [x] 4.6 Create IncidentPolicy (admin/commander/supervisor create, auditor read)
  - [x] 4.7 Create AuditLogPolicy (admin/auditor read only, no write for anyone)
  - [x] 4.8 Create ReportPolicy (admin/commander/auditor read)
  - [x] 4.9 Register all policies in AppServiceProvider

- [x] 5. Wings module
  - [x] 5.1 Create WingForm Livewire Form Object with validation rules
  - [x] 5.2 Create WingManager Livewire component (list, create, edit, delete)
  - [x] 5.3 Create wing-manager.blade.php with dark navy UI, data table, modal

- [x] 6. Aircraft module
  - [x] 6.1 Create AircraftForm Livewire Form Object
  - [x] 6.2 Create AircraftManager Livewire component (list, create, edit, status change, delete)
  - [x] 6.3 Create aircraft-manager.blade.php with status badges, data table, modal

- [x] 7. Maintenance Tasks module
  - [x] 7.1 Create MaintenanceTaskForm Livewire Form Object
  - [x] 7.2 Create MaintenanceTaskManager Livewire component with real-time status updates
  - [x] 7.3 Create maintenance-task-manager.blade.php with priority badges, overdue indicators

- [x] 8. Maintenance Logs module
  - [x] 8.1 Create MaintenanceLogForm Livewire Form Object
  - [x] 8.2 Create MaintenanceLogManager Livewire component (create, approve workflow)
  - [x] 8.3 Create maintenance-log-manager.blade.php with approval status badges

- [x] 9. Flight Logs module
  - [x] 9.1 Create FlightLogForm Livewire Form Object (with arrival > departure validation)
  - [x] 9.2 Create FlightLogManager Livewire component
  - [x] 9.3 Create flight-log-manager.blade.php with telemetry display

- [x] 10. Incident Management module
  - [x] 10.1 Create IncidentForm Livewire Form Object
  - [x] 10.2 Create IncidentManager Livewire component (report, assign investigator, resolve)
  - [x] 10.3 Create incident-manager.blade.php with severity badges, investigation workflow

- [x] 11. Notification system
  - [x] 11.1 Create Laravel Notification classes (TaskAssigned, TaskOverdue, CriticalIncident, LogPendingApproval)
  - [x] 11.2 Create NotificationPanel Livewire component (polls every 30s, mark read)
  - [x] 11.3 Create notification-panel.blade.php dropdown

- [x] 12. Audit Log viewer
  - [x] 12.1 Create AuditLogViewer Livewire component with date/user/event filters
  - [x] 12.2 Create audit-log-viewer.blade.php paginated table

- [x] 13. Dashboard
  - [x] 13.1 Create Dashboard Livewire component with role-scoped stats (polls every 60s)
  - [x] 13.2 Create dashboard.blade.php with stat cards, maintenance chart, incident chart

- [x] 14. Dark navy UI layout
  - [x] 14.1 Rebuild layouts/app.blade.php with dark navy sidebar + top nav
  - [x] 14.2 Rebuild layouts/guest.blade.php with dark navy split-screen auth layout
  - [x] 14.3 Update sidebar navigation with all module links, role-gated visibility

- [x] 15. Extended Personnel module
  - [x] 15.1 Update PersonnelManager Livewire component to include rank, wing assignment, hours display
  - [x] 15.2 Update personnel-manager.blade.php with new fields and dark UI

- [x] 16. Reports module
  - [x] 16.1 Create ReportController with index and generate actions
  - [x] 16.2 Create report views for each report type (aircraft, maintenance, personnel, incident, flight ops)

- [x] 17. Final integration and testing
  - [x] 17.1 Run php artisan migrate:fresh --seed to verify all migrations and seeders
  - [x] 17.2 Run php artisan test to verify all tests pass
  - [x] 17.3 Run npm run build to verify assets compile cleanly
