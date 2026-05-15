# Requirements Document

## Introduction

The Personnel Management feature provides a full CRUD interface for managing employee records within a Laravel 13 application. It uses Blade + Livewire for reactive UI, Tailwind CSS for responsive styling, and SQLite as the database. The feature follows clean architecture principles with a dedicated Service layer, Form Request validation, and Laravel Policies for authorization.

## Glossary

- **Personnel**: An employee record stored in the system.
- **Personnel_Manager**: The Livewire component responsible for listing, creating, editing, and deleting personnel records.
- **Personnel_Service**: The service class that encapsulates all business logic for personnel operations.
- **Personnel_Repository**: (optional layer) Abstraction over Eloquent for data access.
- **Personnel_Policy**: The Laravel Policy class that governs who may perform each action on a Personnel record.
- **Personnel_Form_Request**: The Form Request class that validates incoming personnel data.
- **Admin**: An authenticated user with the `admin` role who may perform all personnel operations.
- **Viewer**: An authenticated user without the `admin` role who may only read personnel records.

---

## Requirements

### Requirement 1: Personnel Record Structure

**User Story:** As an Admin, I want each personnel record to capture essential employee information, so that the organization can maintain an accurate employee directory.

#### Acceptance Criteria

1. THE Personnel SHALL store the following fields: `first_name` (string, required), `last_name` (string, required), `email` (string, unique, required), `phone` (string, nullable), `department` (string, required), `position` (string, required), `hire_date` (date, required), `status` (enum: `active` | `inactive`, required, default `active`), `avatar` (string, nullable), and `notes` (text, nullable).
2. THE Personnel SHALL record `created_at` and `updated_at` timestamps automatically.
3. THE Personnel SHALL use an auto-incrementing integer primary key `id`.

---

### Requirement 2: List Personnel

**User Story:** As an authenticated user, I want to view a paginated list of all personnel records, so that I can browse the employee directory efficiently.

#### Acceptance Criteria

1. WHEN an authenticated user navigates to the personnel index page, THE Personnel_Manager SHALL display personnel records paginated at 15 records per page.
2. WHEN the user enters a search term, THE Personnel_Manager SHALL filter results by `first_name`, `last_name`, `email`, or `department` using a case-insensitive partial match.
3. WHEN the user selects a department filter, THE Personnel_Manager SHALL display only personnel records belonging to that department.
4. WHEN the user selects a status filter, THE Personnel_Manager SHALL display only personnel records with the matching status.
5. THE Personnel_Manager SHALL display each record's full name, email, department, position, hire date, and status badge.
6. WHILE no records match the current filters, THE Personnel_Manager SHALL display an empty-state message.

---

### Requirement 3: Create Personnel

**User Story:** As an Admin, I want to create new personnel records through a validated form, so that new employees are accurately added to the directory.

#### Acceptance Criteria

1. WHEN an Admin submits a valid create form, THE Personnel_Service SHALL persist the new Personnel record and THE Personnel_Manager SHALL display a success notification.
2. WHEN the submitted form contains invalid data, THE Personnel_Form_Request SHALL return field-level validation error messages without persisting any data.
3. THE Personnel_Form_Request SHALL enforce: `first_name` max 100 characters, `last_name` max 100 characters, `email` unique in the `personnel` table and valid email format, `phone` nullable max 20 characters, `department` max 100 characters, `position` max 100 characters, `hire_date` valid date not in the future, `status` one of `active` or `inactive`.
4. IF a Viewer attempts to access the create form, THEN THE Personnel_Policy SHALL deny the action and THE application SHALL return a 403 response.

---

### Requirement 4: View Personnel Detail

**User Story:** As an authenticated user, I want to view the full details of a personnel record, so that I can review all information about an employee.

#### Acceptance Criteria

1. WHEN an authenticated user navigates to a personnel detail page, THE Personnel_Manager SHALL display all fields of the Personnel record.
2. IF the requested Personnel record does not exist, THEN THE application SHALL return a 404 response.

---

### Requirement 5: Edit Personnel

**User Story:** As an Admin, I want to edit existing personnel records, so that employee information stays current.

#### Acceptance Criteria

1. WHEN an Admin submits a valid edit form, THE Personnel_Service SHALL update the Personnel record and THE Personnel_Manager SHALL display a success notification.
2. WHEN the submitted edit form contains invalid data, THE Personnel_Form_Request SHALL return field-level validation error messages without modifying the existing record.
3. THE Personnel_Form_Request SHALL enforce the same rules as Requirement 3 Criterion 3, with the `email` uniqueness rule ignoring the current record's own `id`.
4. IF a Viewer attempts to submit an edit form, THEN THE Personnel_Policy SHALL deny the action and THE application SHALL return a 403 response.

---

### Requirement 6: Delete Personnel

**User Story:** As an Admin, I want to delete personnel records, so that former employees can be removed from the directory.

#### Acceptance Criteria

1. WHEN an Admin confirms deletion of a Personnel record, THE Personnel_Service SHALL soft-delete the record and THE Personnel_Manager SHALL display a success notification.
2. WHILE a Personnel record is soft-deleted, THE Personnel_Manager SHALL exclude it from all list and search results by default.
3. IF a Viewer attempts to delete a Personnel record, THEN THE Personnel_Policy SHALL deny the action and THE application SHALL return a 403 response.
4. IF the Personnel record to be deleted does not exist, THEN THE application SHALL return a 404 response.

---

### Requirement 7: Authorization via Policy

**User Story:** As a system owner, I want all personnel operations to be gated by a Policy, so that only authorized users can modify data.

#### Acceptance Criteria

1. THE Personnel_Policy SHALL grant `viewAny` and `view` permissions to all authenticated users.
2. THE Personnel_Policy SHALL grant `create`, `update`, and `delete` permissions only to users with the `admin` role.
3. WHEN an unauthenticated user attempts to access any personnel route, THE application SHALL redirect the user to the login page.

---

### Requirement 8: Responsive UI

**User Story:** As a user on any device, I want the personnel management interface to be usable on mobile, tablet, and desktop screens, so that I can manage records from any device.

#### Acceptance Criteria

1. THE Personnel_Manager SHALL render a responsive layout using Tailwind CSS utility classes that adapts to screen widths of 320px and above.
2. THE Personnel_Manager SHALL display the personnel list as a stacked card layout on screens narrower than 768px and as a table layout on screens 768px and wider.
3. THE Personnel_Manager SHALL display form inputs in a single-column layout on screens narrower than 768px and a two-column grid on screens 768px and wider.
