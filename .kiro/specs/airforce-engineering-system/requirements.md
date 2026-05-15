
# Requirements Document

## Introduction

The Air Force Engineering Command System is a complete rebuild of the existing personnel management application into a comprehensive, multi-module military engineering command platform. Built on Laravel 12, PHP 8.3+, Livewire 3, Blade, Tailwind CSS, Alpine.js, and SQLite (development) / MySQL (production), the system manages the full operational lifecycle of an Air Force engineering command: personnel, wings, aircraft, maintenance, flight operations, incident management, audit logging, notifications, and analytics.

The system replaces the existing two-role (admin/viewer) personnel CRUD app with a five-role (Admin, Commander, Engineer, Supervisor, Auditor) platform spanning 12 functional modules. All modules share a dark navy/slate military UI, a service-layer architecture, repository pattern, thin controllers, Form Request validation, and Laravel Policies.

---

## Glossary

- **System**: The Air Force Engineering Command System as a whole.
- **Auth_Module**: The authentication and session management subsystem.
- **Dashboard**: The role-aware landing page displaying aggregated statistics and charts.
- **Personnel_Module**: The module managing personnel records, ranks, and role assignments.
- **Wings_Module**: The module managing wing units, commanders, and base locations.
- **Aircraft_Module**: The module managing aircraft records, statuses, and wing assignments.
- **Maintenance_Module**: The module managing maintenance tasks, engineer assignments, and real-time status updates.
- **Maintenance_Log_Module**: The module for logging engineering work performed against aircraft.
- **Flight_Log_Module**: The module recording flight telemetry, GPS data, and flight history.
- **Incident_Module**: The module for reporting, investigating, and resolving incidents.
- **Audit_Module**: The module capturing all user activity, model changes, and authentication events.
- **Notification_Module**: The module delivering in-app and queued alerts for critical events.
- **Reports_Module**: The module generating aggregated reports and export-ready analytics.
- **Admin**: An authenticated user with the `admin` role; has full system access.
- **Commander**: An authenticated user with the `commander` role; manages wings, aircraft, and personnel within their command.
- **Engineer**: An authenticated user with the `engineer` role; performs and logs maintenance work.
- **Supervisor**: An authenticated user with the `supervisor` role; oversees maintenance tasks and personnel activity.
- **Auditor**: An authenticated user with the `auditor` role; has read-only access to audit logs and reports.
- **Personnel**: A record representing a service member with rank, role, and operational data.
- **Wing**: An organizational unit grouping aircraft and personnel under a commander.
- **Aircraft**: A military aircraft record identified by tail number, with status and wing assignment.
- **Maintenance_Task**: A discrete maintenance work order assigned to one or more engineers.
- **Maintenance_Log**: A record of engineering work performed, linked to an aircraft and engineer.
- **Flight_Log**: A record of a completed flight including telemetry, GPS coordinates, speed, and altitude.
- **Incident**: A reported safety or operational event with severity, investigation status, and resolution.
- **Audit_Log**: An immutable record of a user action, model change, or authentication event.
- **Notification**: An in-app alert delivered to one or more users based on system events.
- **Report**: An aggregated, filterable data export covering one or more modules.
- **Policy**: A Laravel Policy class enforcing role-based authorization for a given model.
- **Service**: A PHP service class encapsulating business logic for a module.
- **Repository**: A PHP repository class abstracting Eloquent data access for a module.
- **Form_Request**: A Laravel Form Request class providing validated, authorized input for a controller action.
- **Livewire_Component**: A server-rendered reactive UI component built with Livewire 3.

---

## Requirements

---

### Requirement 1: Authentication and Session Management

**User Story:** As a service member, I want to securely log in to the system with my credentials, so that I can access only the modules and data my role permits.

#### Acceptance Criteria

1. WHEN a user submits valid credentials, THE Auth_Module SHALL authenticate the user, create a session, and redirect the user to the Dashboard.
2. WHEN a user submits invalid credentials, THE Auth_Module SHALL increment the `failed_attempts` counter on the matching User record and record the current timestamp in `last_failed_login`.
3. WHEN a user's `failed_attempts` count reaches 5 within a 15-minute window, THE Auth_Module SHALL lock the account and return an error message stating the account is temporarily locked.
4. WHEN a locked account attempts to log in before the 15-minute lockout period expires, THE Auth_Module SHALL reject the attempt and display the remaining lockout duration in minutes.
5. WHEN a user successfully authenticates after a previous failed attempt, THE Auth_Module SHALL reset `failed_attempts` to 0.
6. WHEN a user requests a password reset, THE Auth_Module SHALL send a password reset link to the registered email address valid for 60 minutes.
7. WHEN a user submits a new password via a valid reset link, THE Auth_Module SHALL update the password hash and invalidate all existing sessions for that user.
8. WHEN an authenticated user requests logout, THE Auth_Module SHALL invalidate the current session and redirect the user to the login page.
9. WHILE a session is active, THE Auth_Module SHALL regenerate the session token on each successful login to prevent session fixation.
10. THE Auth_Module SHALL record each authentication event (login, logout, failed attempt, password reset) as an Audit_Log entry including the user identifier, IP address, user agent, and timestamp.

---

### Requirement 2: Role-Based Access Control

**User Story:** As a system administrator, I want every action in the system to be gated by the authenticated user's role, so that personnel can only access and modify data appropriate to their responsibilities.

#### Acceptance Criteria

1. THE System SHALL enforce one of five roles for every authenticated user: `admin`, `commander`, `engineer`, `supervisor`, or `auditor`.
2. THE System SHALL apply a dedicated Laravel Policy to every model, and every controller action SHALL invoke the corresponding Policy before executing.
3. WHEN an authenticated user attempts an action their Policy denies, THE System SHALL return a 403 HTTP response and log the denied attempt as an Audit_Log entry.
4. WHEN an unauthenticated request reaches any protected route, THE Auth_Module SHALL redirect the request to the login page.
5. THE System SHALL grant `admin` role users full create, read, update, and delete access across all modules.
6. THE System SHALL grant `commander` role users create, read, and update access to Wings, Aircraft, Personnel within their wing, Maintenance_Tasks, and Flight_Logs; and read access to Incidents and Reports.
7. THE System SHALL grant `engineer` role users create and read access to Maintenance_Logs and read access to Maintenance_Tasks assigned to them; and read access to Aircraft and Flight_Logs.
8. THE System SHALL grant `supervisor` role users read and update access to Maintenance_Tasks and Maintenance_Logs; create and read access to Incidents; and read access to Personnel, Aircraft, and Reports.
9. THE System SHALL grant `auditor` role users read-only access to Audit_Logs, Reports, Personnel, Aircraft, Maintenance_Tasks, Maintenance_Logs, Flight_Logs, and Incidents.
10. WHERE role-based middleware is configured, THE System SHALL apply the middleware to all route groups corresponding to each module.

---

### Requirement 3: User Registration and Profile Management

**User Story:** As an Admin, I want to register new user accounts and assign roles, so that new personnel can access the system with the correct permissions from day one.

#### Acceptance Criteria

1. WHEN an Admin submits a valid registration form, THE Auth_Module SHALL create a User record with the specified name, email, hashed password, and role, and THE System SHALL redirect to the user list.
2. WHEN a registration form is submitted with an email already present in the `users` table, THE Auth_Module SHALL return a validation error without creating a duplicate record.
3. THE Form_Request for registration SHALL enforce: `name` required max 255 characters, `email` required valid format unique in `users`, `password` required min 12 characters confirmed, `role` required one of `admin`, `commander`, `engineer`, `supervisor`, `auditor`.
4. WHEN an authenticated user updates their own profile, THE Auth_Module SHALL validate and persist changes to `name` and `email`, and THE System SHALL display a success notification.
5. WHEN an authenticated user changes their password via the profile page, THE Auth_Module SHALL verify the current password before persisting the new hashed password.
6. IF a non-Admin user attempts to change their own role, THEN THE System SHALL deny the action and return a 403 response.

---

### Requirement 4: Dashboard

**User Story:** As an authenticated user, I want a role-aware dashboard that surfaces the most operationally relevant statistics and alerts, so that I can assess the command's status at a glance.

#### Acceptance Criteria

1. WHEN an authenticated user navigates to the Dashboard, THE Dashboard SHALL display summary statistics cards for: total active aircraft, open Maintenance_Tasks by priority, active Personnel count, critical open Incidents, and total Flight_Logs in the last 30 days.
2. WHEN an authenticated user navigates to the Dashboard, THE Dashboard SHALL display a chart showing Maintenance_Task counts grouped by status (`pending`, `in-progress`, `completed`) for the current calendar month.
3. WHEN an authenticated user navigates to the Dashboard, THE Dashboard SHALL display a chart showing Incident counts grouped by severity (`low`, `medium`, `high`, `critical`) for the current calendar month.
4. WHEN a `commander` role user views the Dashboard, THE Dashboard SHALL scope all statistics to the Wings and Aircraft under that commander's command.
5. WHEN a `engineer` role user views the Dashboard, THE Dashboard SHALL display only Maintenance_Tasks assigned to that engineer and Flight_Logs associated with aircraft they have worked on.
6. THE Dashboard SHALL render using the dark navy (#0F172A) and slate color palette with a sidebar navigation layout.
7. THE Dashboard SHALL display a sidebar navigation listing all modules accessible to the authenticated user's role.
8. WHILE Dashboard data is loading, THE Dashboard SHALL display skeleton loading placeholders for each statistics card and chart.
9. THE Dashboard SHALL refresh statistics automatically every 60 seconds without a full page reload using Livewire polling.

---

### Requirement 5: Personnel Management

**User Story:** As an Admin or Commander, I want to manage personnel records including rank, role, and operational hours, so that the command maintains an accurate and current roster.

#### Acceptance Criteria

1. THE Personnel_Module SHALL store the following fields per Personnel record: `first_name` (string, required, max 100), `last_name` (string, required, max 100), `rank` (string, required, max 50), `email` (string, required, unique, valid email format), `role` (enum: `admin` | `commander` | `engineer` | `supervisor` | `auditor`, required), `total_hours_logged` (decimal, default 0.00), `failed_attempts` (integer, default 0), `last_failed_login` (datetime, nullable), `created_at`, `updated_at`, and `deleted_at` for soft deletes.
2. WHEN an Admin or Commander navigates to the personnel list, THE Personnel_Module SHALL display Personnel records paginated at 20 records per page, showing full name, rank, role, email, and total hours logged.
3. WHEN a user enters a search term in the personnel list, THE Personnel_Module SHALL filter results by `first_name`, `last_name`, `email`, or `rank` using a case-insensitive partial match.
4. WHEN a user selects a role filter, THE Personnel_Module SHALL display only Personnel records with the matching role.
5. WHEN an Admin submits a valid create form, THE Personnel_Module SHALL persist the new Personnel record and display a success notification.
6. WHEN an Admin submits a valid edit form, THE Personnel_Module SHALL update the Personnel record and display a success notification.
7. WHEN an Admin confirms deletion of a Personnel record, THE Personnel_Module SHALL soft-delete the record and display a success notification.
8. IF a non-Admin, non-Commander user attempts to create, update, or delete a Personnel record, THEN THE Personnel_Module SHALL deny the action and return a 403 response.
9. WHEN an authenticated user navigates to a Personnel profile page, THE Personnel_Module SHALL display all fields, the personnel's associated Wing, recent Maintenance_Logs, and a total hours summary.
10. THE Personnel_Module SHALL update `total_hours_logged` automatically whenever a Maintenance_Log linked to that Personnel record is created or updated with a time value.
11. WHILE a Personnel record is soft-deleted, THE Personnel_Module SHALL exclude it from all list, search, and filter results by default.

---

### Requirement 6: Wings Management

**User Story:** As an Admin or Commander, I want to create and manage wing units with assigned commanders and base locations, so that the organizational structure of the command is accurately represented.

#### Acceptance Criteria

1. THE Wings_Module SHALL store the following fields per Wing record: `name` (string, required, unique, max 150), `code` (string, required, unique, max 20), `base_location` (string, required, max 255), `commander_id` (foreign key to `users`, nullable), `status` (enum: `active` | `inactive`, required, default `active`), `established_date` (date, nullable), `description` (text, nullable), `created_at`, `updated_at`.
2. WHEN an Admin or Commander navigates to the wings list, THE Wings_Module SHALL display all Wing records with name, code, base location, commander name, aircraft count, and personnel count.
3. WHEN an Admin submits a valid create form for a Wing, THE Wings_Module SHALL persist the Wing record and display a success notification.
4. WHEN an Admin assigns a commander to a Wing, THE Wings_Module SHALL validate that the assigned user has the `commander` role before persisting the assignment.
5. IF a user with the `commander` role is assigned to more than one Wing simultaneously, THEN THE Wings_Module SHALL reject the second assignment and return a validation error.
6. WHEN an Admin submits a valid edit form for a Wing, THE Wings_Module SHALL update the Wing record and display a success notification.
7. WHEN an Admin navigates to a Wing detail page, THE Wings_Module SHALL display the wing's details, a list of assigned Aircraft, and a list of assigned Personnel.
8. IF a non-Admin user attempts to create or delete a Wing, THEN THE Wings_Module SHALL deny the action and return a 403 response.

---

### Requirement 7: Aircraft Management

**User Story:** As an Admin or Commander, I want to manage aircraft records including tail numbers, operational status, and wing assignments, so that the command has an accurate inventory of its fleet.

#### Acceptance Criteria

1. THE Aircraft_Module SHALL store the following fields per Aircraft record: `tail_number` (string, required, unique, max 20), `model` (string, required, max 100), `manufacturer` (string, required, max 100), `year_manufactured` (integer, nullable), `wing_id` (foreign key to `wings`, nullable), `status` (enum: `active` | `maintenance` | `grounded` | `retired`, required, default `active`), `last_maintenance_date` (date, nullable), `total_flight_hours` (decimal, default 0.00), `notes` (text, nullable), `created_at`, `updated_at`.
2. WHEN an Admin or Commander navigates to the aircraft list, THE Aircraft_Module SHALL display Aircraft records paginated at 20 per page, showing tail number, model, wing name, status badge, total flight hours, and last maintenance date.
3. WHEN a user enters a search term in the aircraft list, THE Aircraft_Module SHALL filter results by `tail_number` or `model` using a case-insensitive partial match.
4. WHEN a user selects a status filter, THE Aircraft_Module SHALL display only Aircraft records with the matching status.
5. WHEN an Admin or Commander submits a valid create form, THE Aircraft_Module SHALL persist the Aircraft record and display a success notification.
6. WHEN an Admin or Commander submits a valid edit form, THE Aircraft_Module SHALL update the Aircraft record and display a success notification.
7. WHEN an Admin navigates to an Aircraft detail page, THE Aircraft_Module SHALL display all fields, a maintenance history list (most recent 10 Maintenance_Logs), open Maintenance_Tasks, and a Flight_Log summary.
8. WHEN an Aircraft's status is changed to `maintenance` or `grounded`, THE Aircraft_Module SHALL create an Audit_Log entry recording the previous status, new status, user, and timestamp.
9. IF a non-Admin, non-Commander user attempts to create, update, or delete an Aircraft record, THEN THE Aircraft_Module SHALL deny the action and return a 403 response.
10. THE Aircraft_Module SHALL update `total_flight_hours` automatically whenever a Flight_Log linked to that Aircraft is created or updated with a duration value.

---

### Requirement 8: Maintenance Task Management

**User Story:** As a Supervisor or Admin, I want to create and manage maintenance tasks with priority levels and engineer assignments, so that all required maintenance work is tracked from creation to completion.

#### Acceptance Criteria

1. THE Maintenance_Module SHALL store the following fields per Maintenance_Task record: `title` (string, required, max 200), `description` (text, nullable), `aircraft_id` (foreign key to `aircraft`, required), `assigned_to` (foreign key to `users`, nullable), `created_by` (foreign key to `users`, required), `priority` (enum: `low` | `medium` | `high` | `critical`, required, default `medium`), `status` (enum: `pending` | `in-progress` | `completed`, required, default `pending`), `due_date` (date, nullable), `completed_at` (datetime, nullable), `created_at`, `updated_at`.
2. WHEN an Admin or Supervisor submits a valid create form for a Maintenance_Task, THE Maintenance_Module SHALL persist the task and display a success notification.
3. WHEN a Maintenance_Task is assigned to an Engineer, THE Maintenance_Module SHALL trigger a Notification to that Engineer containing the task title, aircraft tail number, priority, and due date.
4. WHEN an Engineer or Supervisor updates the status of a Maintenance_Task to `completed`, THE Maintenance_Module SHALL record the current timestamp in `completed_at`.
5. WHEN a Maintenance_Task's priority is `critical`, THE Maintenance_Module SHALL display the task with a visually distinct critical indicator in all list views.
6. WHEN a user navigates to the maintenance task list, THE Maintenance_Module SHALL display tasks paginated at 20 per page with real-time status updates via a Livewire_Component.
7. WHEN a user applies a priority filter, THE Maintenance_Module SHALL display only Maintenance_Tasks matching the selected priority.
8. WHEN a user applies a status filter, THE Maintenance_Module SHALL display only Maintenance_Tasks matching the selected status.
9. WHEN a Maintenance_Task's `due_date` passes and the status is not `completed`, THE Maintenance_Module SHALL display the task with an overdue indicator.
10. IF a non-Admin, non-Supervisor user attempts to create or delete a Maintenance_Task, THEN THE Maintenance_Module SHALL deny the action and return a 403 response.
11. WHEN an Engineer is assigned to a Maintenance_Task, THE Maintenance_Module SHALL validate that the assigned user has the `engineer` role before persisting the assignment.

---

### Requirement 9: Maintenance Log Management

**User Story:** As an Engineer, I want to log the engineering work I perform on aircraft, so that a complete and auditable maintenance history is maintained for each aircraft.

#### Acceptance Criteria

1. THE Maintenance_Log_Module SHALL store the following fields per Maintenance_Log record: `maintenance_task_id` (foreign key to `maintenance_tasks`, nullable), `aircraft_id` (foreign key to `aircraft`, required), `engineer_id` (foreign key to `users`, required), `work_performed` (text, required), `parts_used` (text, nullable), `hours_spent` (decimal, required, min 0.01), `log_date` (date, required), `status` (enum: `draft` | `submitted` | `approved`, required, default `draft`), `approved_by` (foreign key to `users`, nullable), `approved_at` (datetime, nullable), `created_at`, `updated_at`.
2. WHEN an Engineer submits a valid Maintenance_Log entry, THE Maintenance_Log_Module SHALL persist the log, increment the associated Personnel record's `total_hours_logged` by `hours_spent`, and display a success notification.
3. WHEN a Supervisor approves a Maintenance_Log, THE Maintenance_Log_Module SHALL set `status` to `approved`, record the approving user in `approved_by`, and record the current timestamp in `approved_at`.
4. WHEN a user navigates to the maintenance log list, THE Maintenance_Log_Module SHALL display logs paginated at 20 per page, showing aircraft tail number, engineer name, work performed summary, hours spent, log date, and status badge.
5. WHEN a user filters by aircraft, THE Maintenance_Log_Module SHALL display only Maintenance_Logs linked to the selected Aircraft.
6. WHEN a user filters by engineer, THE Maintenance_Log_Module SHALL display only Maintenance_Logs created by the selected Engineer.
7. IF a non-Engineer, non-Admin user attempts to create a Maintenance_Log, THEN THE Maintenance_Log_Module SHALL deny the action and return a 403 response.
8. IF an Engineer attempts to edit a Maintenance_Log with status `approved`, THEN THE Maintenance_Log_Module SHALL deny the edit and return a validation error.

---

### Requirement 10: Flight Log Management

**User Story:** As a Commander or Admin, I want to record and review flight logs with telemetry data, so that the command has a complete and searchable history of all flight operations.

#### Acceptance Criteria

1. THE Flight_Log_Module SHALL store the following fields per Flight_Log record: `aircraft_id` (foreign key to `aircraft`, required), `pilot_id` (foreign key to `users`, required), `co_pilot_id` (foreign key to `users`, nullable), `departure_location` (string, required, max 255), `arrival_location` (string, required, max 255), `departure_time` (datetime, required), `arrival_time` (datetime, required), `flight_duration_minutes` (integer, computed from departure and arrival times), `max_altitude_ft` (integer, nullable), `max_speed_knots` (integer, nullable), `gps_track` (json, nullable, stores array of latitude/longitude waypoints), `mission_type` (enum: `training` | `operational` | `test` | `ferry`, required), `notes` (text, nullable), `created_at`, `updated_at`.
2. WHEN a Commander or Admin submits a valid Flight_Log entry, THE Flight_Log_Module SHALL persist the log, compute `flight_duration_minutes` from `departure_time` and `arrival_time`, update the associated Aircraft's `total_flight_hours`, and display a success notification.
3. IF `arrival_time` is not after `departure_time`, THEN THE Flight_Log_Module SHALL return a validation error without persisting the record.
4. WHEN a user navigates to the flight log list, THE Flight_Log_Module SHALL display logs paginated at 20 per page, showing aircraft tail number, pilot name, departure location, arrival location, departure time, duration, and mission type.
5. WHEN a user filters by aircraft, THE Flight_Log_Module SHALL display only Flight_Logs linked to the selected Aircraft.
6. WHEN a user filters by mission type, THE Flight_Log_Module SHALL display only Flight_Logs with the matching mission type.
7. WHEN a user filters by date range, THE Flight_Log_Module SHALL display only Flight_Logs where `departure_time` falls within the specified range.
8. WHEN a user navigates to a Flight_Log detail page, THE Flight_Log_Module SHALL display all fields and, WHERE `gps_track` data is present, THE Flight_Log_Module SHALL render the waypoints in a map-ready data structure accessible to a front-end mapping library.
9. IF a non-Commander, non-Admin user attempts to create or delete a Flight_Log, THEN THE Flight_Log_Module SHALL deny the action and return a 403 response.
10. THE Flight_Log_Module SHALL record the creation of each Flight_Log as an Audit_Log entry.

---

### Requirement 11: Incident Management

**User Story:** As a Supervisor or Commander, I want to report, investigate, and resolve operational incidents, so that safety events are documented, tracked, and closed with a full audit trail.

#### Acceptance Criteria

1. THE Incident_Module SHALL store the following fields per Incident record: `title` (string, required, max 200), `description` (text, required), `aircraft_id` (foreign key to `aircraft`, nullable), `reported_by` (foreign key to `users`, required), `assigned_investigator_id` (foreign key to `users`, nullable), `severity` (enum: `low` | `medium` | `high` | `critical`, required), `status` (enum: `open` | `under-investigation` | `resolved` | `closed`, required, default `open`), `incident_date` (datetime, required), `resolution_notes` (text, nullable), `resolved_at` (datetime, nullable), `created_at`, `updated_at`.
2. WHEN a Supervisor or Commander submits a valid incident report, THE Incident_Module SHALL persist the Incident record and trigger a Notification to all Admin users containing the incident title, severity, and reporting user.
3. WHEN an Incident has severity `critical`, THE Incident_Module SHALL additionally trigger a Notification to all Commander and Admin users immediately upon creation.
4. WHEN an Admin or Supervisor assigns an investigator to an Incident, THE Incident_Module SHALL validate that the assigned user has the `supervisor` or `commander` role before persisting the assignment.
5. WHEN an investigator updates an Incident's status to `resolved`, THE Incident_Module SHALL require `resolution_notes` to be non-empty and record the current timestamp in `resolved_at`.
6. WHEN a user navigates to the incident list, THE Incident_Module SHALL display Incidents paginated at 20 per page, showing title, severity badge, status badge, reported by, incident date, and assigned investigator.
7. WHEN a user applies a severity filter, THE Incident_Module SHALL display only Incidents with the matching severity.
8. WHEN a user applies a status filter, THE Incident_Module SHALL display only Incidents with the matching status.
9. IF a non-Supervisor, non-Commander, non-Admin user attempts to create an Incident, THEN THE Incident_Module SHALL deny the action and return a 403 response.
10. THE Incident_Module SHALL record every status change as an Audit_Log entry including the previous status, new status, user, and timestamp.

---

### Requirement 12: Audit Logging

**User Story:** As an Auditor or Admin, I want a complete, immutable log of all user actions and model changes, so that the command can review activity for compliance, security, and investigation purposes.

#### Acceptance Criteria

1. THE Audit_Module SHALL store the following fields per Audit_Log record: `user_id` (foreign key to `users`, nullable), `event` (string, required, max 100, e.g. `login`, `logout`, `created`, `updated`, `deleted`, `policy_denied`), `auditable_type` (string, nullable, the model class name), `auditable_id` (integer, nullable, the model primary key), `old_values` (json, nullable), `new_values` (json, nullable), `ip_address` (string, nullable, max 45), `user_agent` (string, nullable, max 500), `created_at` (datetime, required).
2. THE Audit_Module SHALL record an Audit_Log entry for every create, update, and delete operation on the following models: User, Personnel, Wing, Aircraft, Maintenance_Task, Maintenance_Log, Flight_Log, Incident.
3. THE Audit_Module SHALL record `old_values` and `new_values` as JSON snapshots of the changed attributes for every update operation.
4. THE Audit_Module SHALL record an Audit_Log entry for every authentication event: successful login, failed login, logout, password reset request, and password reset completion.
5. THE Audit_Module SHALL record an Audit_Log entry for every Policy denial, including the user, the denied action, and the target model.
6. WHEN an Auditor or Admin navigates to the audit dashboard, THE Audit_Module SHALL display Audit_Log entries paginated at 50 per page, showing event, user name, auditable type, auditable ID, IP address, and timestamp.
7. WHEN a user applies a date range filter on the audit dashboard, THE Audit_Module SHALL display only Audit_Log entries where `created_at` falls within the specified range.
8. WHEN a user applies a user filter on the audit dashboard, THE Audit_Module SHALL display only Audit_Log entries associated with the selected user.
9. WHEN a user applies an event type filter on the audit dashboard, THE Audit_Module SHALL display only Audit_Log entries with the matching event value.
10. THE Audit_Module SHALL never allow any user to update or delete an Audit_Log entry; all Audit_Log records are immutable once created.
11. IF a non-Auditor, non-Admin user attempts to access the audit dashboard, THEN THE Audit_Module SHALL deny the action and return a 403 response.

---

### Requirement 13: Notification System

**User Story:** As a user, I want to receive in-app notifications for critical events such as task assignments, critical incidents, and maintenance alerts, so that I can respond promptly without having to poll each module manually.

#### Acceptance Criteria

1. THE Notification_Module SHALL store the following fields per Notification record: `user_id` (foreign key to `users`, required), `type` (string, required, max 100), `title` (string, required, max 200), `body` (text, required), `data` (json, nullable, contextual payload), `read_at` (datetime, nullable), `created_at`, `updated_at`.
2. WHEN a Maintenance_Task is assigned to an Engineer, THE Notification_Module SHALL create a Notification for that Engineer with type `task_assigned`.
3. WHEN a Maintenance_Task's `due_date` passes and status is not `completed`, THE Notification_Module SHALL create a Notification for the assigned Engineer and the creating Supervisor with type `task_overdue`.
4. WHEN an Incident with severity `critical` is created, THE Notification_Module SHALL create Notifications for all users with the `admin` or `commander` role with type `critical_incident`.
5. WHEN a Maintenance_Log is submitted for approval, THE Notification_Module SHALL create a Notification for all Supervisors with type `log_pending_approval`.
6. WHEN an authenticated user navigates to any page, THE Notification_Module SHALL display an unread notification count badge in the navigation header using a Livewire_Component that polls every 30 seconds.
7. WHEN an authenticated user opens the notification panel, THE Notification_Module SHALL display the 20 most recent Notifications for that user, ordered by `created_at` descending, showing type, title, body, and relative timestamp.
8. WHEN a user marks a Notification as read, THE Notification_Module SHALL set `read_at` to the current timestamp and decrement the unread count badge.
9. WHEN a user marks all Notifications as read, THE Notification_Module SHALL set `read_at` to the current timestamp on all unread Notifications for that user.
10. THE Notification_Module SHALL dispatch Notification creation through Laravel's queue system to avoid blocking the HTTP response.

---

### Requirement 14: Reports and Analytics

**User Story:** As an Admin, Commander, or Auditor, I want to generate and export reports covering aircraft, maintenance, personnel, and incidents, so that command leadership can make data-driven operational decisions.

#### Acceptance Criteria

1. THE Reports_Module SHALL provide the following report types: Aircraft Status Report, Maintenance Summary Report, Personnel Activity Report, Incident Summary Report, and Flight Operations Report.
2. WHEN a user generates an Aircraft Status Report, THE Reports_Module SHALL display a summary of all Aircraft grouped by status (`active`, `maintenance`, `grounded`, `retired`) with counts and percentage breakdowns.
3. WHEN a user generates a Maintenance Summary Report, THE Reports_Module SHALL display Maintenance_Task counts grouped by priority and status, average completion time in days, and a list of overdue tasks.
4. WHEN a user generates a Personnel Activity Report, THE Reports_Module SHALL display each Personnel record's total hours logged, number of Maintenance_Logs submitted, and number of Maintenance_Tasks completed within the selected date range.
5. WHEN a user generates an Incident Summary Report, THE Reports_Module SHALL display Incident counts grouped by severity and status, average resolution time in days, and a list of unresolved critical Incidents.
6. WHEN a user generates a Flight Operations Report, THE Reports_Module SHALL display total flight hours per Aircraft, total flights per pilot, and mission type distribution within the selected date range.
7. WHEN a user applies a date range filter to any report, THE Reports_Module SHALL scope all aggregated data to records whose primary date field falls within the specified range.
8. WHEN a user applies a wing filter to any report, THE Reports_Module SHALL scope all aggregated data to records associated with the selected Wing.
9. THE Reports_Module SHALL render report data in both a paginated data table and a summary chart on the same page.
10. THE Reports_Module SHALL provide an export-ready data structure for each report type, returning a structured array suitable for CSV or PDF generation by a downstream export service.
11. IF a non-Admin, non-Commander, non-Auditor user attempts to access the Reports module, THEN THE Reports_Module SHALL deny the action and return a 403 response.

---

### Requirement 15: User Interface and Navigation

**User Story:** As a user on any device, I want a consistent, professional military-themed interface with intuitive navigation, so that I can operate the system efficiently in both office and field environments.

#### Acceptance Criteria

1. THE System SHALL apply a dark navy (#0F172A) primary background and slate color palette to all authenticated pages.
2. THE System SHALL render a persistent sidebar navigation on screens 1024px and wider, listing all modules accessible to the authenticated user's role with icons and labels.
3. THE System SHALL collapse the sidebar into a hamburger-triggered drawer on screens narrower than 1024px.
4. THE System SHALL display a top navigation bar on all authenticated pages containing the application name, the authenticated user's name and role badge, an unread notification count badge, and a logout link.
5. THE System SHALL render all data tables with alternating row shading, sortable column headers, and a per-page selector offering 10, 20, and 50 records per page.
6. THE System SHALL display status and severity values as color-coded badges: `active`/`completed`/`resolved` in green, `pending`/`open` in yellow, `in-progress`/`under-investigation` in blue, `grounded`/`critical` in red, `retired`/`inactive` in gray.
7. THE System SHALL display all forms with labeled inputs, inline validation error messages, and a primary action button using the military color palette.
8. THE System SHALL render all pages as responsive layouts that function correctly at screen widths of 320px and above.
9. WHEN a destructive action (delete, status change to `grounded` or `retired`) is triggered, THE System SHALL display a confirmation modal before executing the action.
10. THE System SHALL display a flash notification banner at the top of the page for 4 seconds after any successful create, update, or delete operation.

---

### Requirement 16: Data Integrity and Architecture

**User Story:** As a system architect, I want the application to follow clean architecture principles with proper Eloquent relationships and optimized queries, so that the system remains maintainable and performant as data volumes grow.

#### Acceptance Criteria

1. THE System SHALL implement a Service class for each module encapsulating all business logic, with controllers limited to input validation delegation and response formatting.
2. THE System SHALL implement a Repository class for each module abstracting all Eloquent queries, with Service classes depending on Repository interfaces rather than concrete Eloquent models.
3. THE System SHALL define Eloquent relationships for all foreign key associations: Wing `hasMany` Aircraft, Wing `hasMany` Personnel (via assignment), Aircraft `hasMany` Maintenance_Tasks, Aircraft `hasMany` Maintenance_Logs, Aircraft `hasMany` Flight_Logs, User `hasMany` Maintenance_Logs (as engineer), User `hasMany` Incidents (as reporter), User `hasMany` Notifications.
4. THE System SHALL apply database indexes to all foreign key columns and all columns used in WHERE clauses in list and filter queries.
5. THE System SHALL use Eloquent eager loading (`with()`) for all list queries that display related model data to prevent N+1 query problems.
6. THE System SHALL apply soft deletes to the following models: User, Personnel, Wing, Aircraft, Maintenance_Task, Maintenance_Log, Flight_Log, Incident.
7. THE System SHALL use Laravel Form Requests for all create and update operations, with authorization checks implemented in the `authorize()` method of each Form_Request.
8. THE System SHALL use database transactions for any operation that writes to more than one table, ensuring atomicity.
9. THE System SHALL be compatible with both SQLite (development) and MySQL 8.0+ (production) without requiring schema changes.
10. THE System SHALL paginate all list queries using Laravel's built-in cursor or length-aware pagination, with a configurable default page size per module.
