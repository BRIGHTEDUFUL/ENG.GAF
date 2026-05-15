# Implementation Plan: Personnel Management

## Overview

Implement a full CRUD personnel management interface using Laravel 12, Livewire, Blade, and Tailwind CSS. The implementation follows clean architecture: Eloquent model → Service layer → Livewire component → Blade views. Tasks are ordered so each step integrates cleanly into the previous one, with no orphaned code.

## Tasks

- [x] 1. Extend the User model with role support
  - Create migration `add_role_to_users_table` that adds a `role VARCHAR(20) DEFAULT 'viewer'` column to the `users` table
  - Add `role` to the `$fillable` array (or `#[Fillable]` attribute) on `App\Models\User`
  - Add a helper method `isAdmin(): bool` that returns `$this->role === 'admin'`
  - Update `database/factories/UserFactory.php` to include `role` with a default of `'viewer'`
  - _Requirements: 7.1, 7.2_

- [x] 2. Implement the Personnel Policy
  - [x] 2.1 Create `App\Policies\PersonnelPolicy` with methods `viewAny`, `view`, `create`, `update`, `delete`
    - `viewAny` and `view` return `true` for any authenticated user
    - `create`, `update`, `delete` return `true` only when `$user->isAdmin()` is `true`
    - Register the policy in `App\Providers\AppServiceProvider` using `Gate::policy(Personnel::class, PersonnelPolicy::class)`
    - _Requirements: 7.1, 7.2_

  - [ ]* 2.2 Write property test for viewer cannot mutate (Property 7)
    - **Property 7: Viewer cannot mutate**
    - Generate random non-admin users; assert `create()`, `update()`, and `delete()` all return `false`
    - **Validates: Requirements 3.4, 5.4, 6.3, 7.2**

  - [ ]* 2.3 Write property test for all authenticated users can view (Property 8)
    - **Property 8: All authenticated users can view**
    - Generate random users with any role; assert `viewAny()` and `view()` return `true`
    - **Validates: Requirements 7.1**

  - [ ]* 2.4 Write unit tests for PersonnelPolicy
    - Test admin can `viewAny`, `view`, `create`, `update`, `delete`
    - Test viewer can `viewAny` and `view` but not `create`, `update`, `delete`
    - _Requirements: 7.1, 7.2_

- [x] 3. Implement the PersonnelService
  - [x] 3.1 Create `App\Services\PersonnelService` implementing `list()`, `find()`, `create()`, `update()`, `delete()`, and `departments()`
    - `list(array $filters, int $perPage = 15)` chains `scopeSearch`, `scopeInDepartment`, `scopeWithStatus` based on non-empty filter keys and returns a `LengthAwarePaginator`
    - `find(int $id)` calls `Personnel::findOrFail($id)` (throws `ModelNotFoundException` on miss)
    - `create(array $data)` calls `Personnel::create($data)` and returns the new model
    - `update(Personnel $personnel, array $data)` calls `$personnel->update($data)` and returns the updated model
    - `delete(Personnel $personnel)` calls `$personnel->delete()` (soft-delete) and returns `true`
    - `departments()` returns a distinct, sorted array of department strings from non-deleted records
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 3.1, 4.2, 5.1, 6.1, 6.2_

  - [ ]* 3.2 Write property test for search filters are inclusive (Property 1)
    - **Property 1: Search filters are inclusive**
    - Generate random personnel datasets and random non-empty search terms; assert every result contains the term (case-insensitively) in `first_name`, `last_name`, `email`, or `department`
    - **Validates: Requirements 2.2**

  - [ ]* 3.3 Write property test for department filter is exact (Property 2)
    - **Property 2: Department filter is exact**
    - Generate random personnel datasets and random department strings; assert every result's `department` equals the filter value exactly
    - **Validates: Requirements 2.3**

  - [ ]* 3.4 Write property test for status filter is exact (Property 3)
    - **Property 3: Status filter is exact**
    - Generate random personnel datasets with mixed statuses; for each status value assert every result's `status` equals the filter value
    - **Validates: Requirements 2.4**

  - [ ]* 3.5 Write property test for create then retrieve round-trip (Property 4)
    - **Property 4: Create then retrieve round-trip**
    - Generate random valid personnel data arrays; assert `find(create($data)->id)` returns a record whose fields match `$data`
    - **Validates: Requirements 1.1, 3.1**

  - [ ]* 3.6 Write property test for soft-delete excludes from list (Property 5)
    - **Property 5: Soft-delete excludes from list**
    - Generate random personnel records; after `delete()` assert the record does not appear in `list([])` and `deleted_at` is non-null
    - **Validates: Requirements 6.1, 6.2**

  - [ ]* 3.7 Write property test for update preserves unmodified fields (Property 9)
    - **Property 9: Update preserves unmodified fields**
    - Generate random personnel records and random partial update arrays (subset of fields); assert updated fields match new values and all other fields remain unchanged
    - **Validates: Requirements 5.1**

  - [ ]* 3.8 Write unit tests for PersonnelService
    - Test `list()` returns paginated results (15 per page)
    - Test `create()` persists a record and returns the model
    - Test `update()` modifies only the supplied fields
    - Test `delete()` soft-deletes the record (sets `deleted_at`)
    - Test `find()` throws `ModelNotFoundException` for missing/deleted records
    - Test `departments()` returns a sorted, distinct list
    - _Requirements: 2.1, 3.1, 5.1, 6.1, 6.2_

- [x] 4. Checkpoint — Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [x] 5. Create Form Request validation classes and Livewire Form Object
  - [x] 5.1 Create `App\Http\Requests\StorePersonnelRequest` with all validation rules
    - Rules: `first_name` required|string|max:100, `last_name` required|string|max:100, `email` required|email|max:255|unique:personnel,email, `phone` nullable|string|max:20, `department` required|string|max:100, `position` required|string|max:100, `hire_date` required|date|before_or_equal:today, `status` required|in:active,inactive, `avatar` nullable|string, `notes` nullable|string
    - Override `authorize()` to return `true` (authorization handled by Policy)
    - _Requirements: 3.2, 3.3_

  - [x] 5.2 Create `App\Http\Requests\UpdatePersonnelRequest` with the same rules
    - `email` uniqueness rule ignores the current record: `unique:personnel,email,{$this->route('personnel')->id}`
    - _Requirements: 5.2, 5.3_

  - [x] 5.3 Create `App\Livewire\Forms\PersonnelForm` Livewire Form Object
    - Declare public properties for all ten fields
    - Add `#[Rule]` attributes (or `rules()` method) mirroring `StorePersonnelRequest`
    - Add a `setPersonnel(Personnel $personnel)` method that fills all properties from the model
    - Add a `reset()` override that clears all fields back to defaults
    - The `email` uniqueness rule must accept an optional `$ignoreId` parameter so the update path can exclude the current record
    - _Requirements: 3.2, 3.3, 5.2, 5.3_

  - [ ]* 5.4 Write property test for invalid data is rejected without side effects (Property 6)
    - **Property 6: Invalid data is rejected without side effects**
    - Generate data arrays violating at least one rule (whitespace-only names, oversized strings, future dates, invalid status, duplicate email); assert validation fails and the record count is unchanged
    - **Validates: Requirements 3.2, 3.3, 5.2**

  - [ ]* 5.5 Write unit tests for PersonnelForm validation
    - Test valid data passes all rules
    - Test empty `first_name` / `last_name` fails
    - Test whitespace-only `first_name` / `last_name` fails
    - Test invalid email format fails
    - Test duplicate email fails (unique rule)
    - Test `hire_date` in the future fails
    - Test `status` outside `active|inactive` fails
    - Test `phone` exceeding 20 characters fails
    - _Requirements: 3.2, 3.3, 5.2, 5.3_

- [x] 6. Implement the PersonnelManager Livewire component
  - [x] 6.1 Create `App\Livewire\PersonnelManager` with all public properties and wire-up
    - Declare `$search`, `$departmentFilter`, `$statusFilter`, `$showModal`, `$showDeleteConfirm`, `$editingId`, and `$form` (PersonnelForm)
    - Inject `PersonnelService` via the constructor
    - Implement `render()`: calls `PersonnelService::list()` with current filters and passes the paginator and departments list to the view
    - Add `WithPagination` trait and reset pagination in `updatedSearch()`, `updatedDepartmentFilter()`, `updatedStatusFilter()`
    - _Requirements: 2.1, 2.2, 2.3, 2.4_

  - [x] 6.2 Implement create and edit modal actions
    - `openCreate()`: resets form, sets `$editingId = null`, sets `$showModal = true`; authorize `create` via `$this->authorize('create', Personnel::class)`
    - `openEdit(int $id)`: loads record via `PersonnelService::find()`, calls `$this->form->setPersonnel()`, sets `$editingId`, sets `$showModal = true`; authorize `update`
    - `save()`: validates form, calls `PersonnelService::create()` or `PersonnelService::update()` based on `$editingId`, dispatches a success notification, closes modal, resets form
    - Catch `AuthorizationException` and dispatch an "Access denied" notification
    - _Requirements: 3.1, 3.2, 3.4, 5.1, 5.2, 5.4_

  - [x] 6.3 Implement delete confirmation actions
    - `confirmDelete(int $id)`: stores the pending ID, sets `$showDeleteConfirm = true`; authorize `delete`
    - `delete()`: calls `PersonnelService::find()` then `PersonnelService::delete()`, dispatches success notification, closes dialog
    - Catch `ModelNotFoundException` and dispatch a "Record not found" notification
    - _Requirements: 6.1, 6.3, 6.4_

  - [ ]* 6.4 Write Livewire feature tests for PersonnelManager
    - Test authenticated user sees paginated list
    - Test admin can open create modal, submit valid form, see success notification
    - Test admin can open edit modal, submit valid form, see success notification
    - Test admin can confirm delete, see record removed from list
    - Test viewer sees list but cannot trigger create/edit/delete (403)
    - Test unauthenticated user is redirected to login
    - Test search input filters the displayed list
    - Test department and status filters narrow the list
    - Test empty state message shown when no records match
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.6, 3.1, 3.4, 5.1, 5.4, 6.1, 6.3, 7.3_

- [x] 7. Build the Blade views
  - [x] 7.1 Create `resources/views/livewire/personnel-manager.blade.php`
    - Render a responsive toolbar: search input, department dropdown, status dropdown, and "Add Personnel" button (hidden for viewers via `@can`)
    - Render a table layout on `md:` breakpoints and a stacked card layout on mobile (below `md:`)
    - Each row/card shows full name, email, department, position, hire date, and a status badge
    - Include Edit and Delete action buttons visible only to admins (`@can`)
    - Render an empty-state message when the paginator is empty
    - Include pagination links (`{{ $personnel->links() }}`)
    - _Requirements: 2.1, 2.5, 2.6, 8.1, 8.2_

  - [x] 7.2 Create the create/edit modal inside `personnel-manager.blade.php`
    - Modal is toggled by `$showModal`; use Livewire `wire:model` bindings for all form fields
    - Form layout: single-column on mobile, two-column grid on `md:` breakpoints
    - Display inline validation error messages under each field using `@error`
    - Include Save and Cancel buttons; Cancel calls `$set('showModal', false)` and resets the form
    - _Requirements: 3.2, 3.3, 5.2, 5.3, 8.3_

  - [x] 7.3 Create the delete confirmation dialog inside `personnel-manager.blade.php`
    - Dialog is toggled by `$showDeleteConfirm`
    - Shows a confirmation message with the employee's full name
    - Confirm button calls `delete()`; Cancel button closes the dialog
    - _Requirements: 6.1_

- [x] 8. Register routes and wire up navigation
  - Add the authenticated route group in `routes/web.php`: `Route::get('/personnel', PersonnelManager::class)->name('personnel.index')`
  - Ensure the `auth` middleware is applied so unauthenticated users are redirected to login
  - Add a navigation link to the personnel index in the application layout (if a layout exists)
  - _Requirements: 7.3_

- [x] 9. Add database seeder for development data
  - Create `database/factories/PersonnelFactory.php` using Faker to generate realistic personnel records across multiple departments and both statuses
  - Update `database/seeders/DatabaseSeeder.php` to seed one admin user, one viewer user, and at least 30 personnel records
  - _Requirements: 1.1, 1.2, 1.3_

- [x] 10. Final checkpoint — Ensure all tests pass
  - Run the full test suite (`php artisan test`)
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for a faster MVP
- Property-based tests use **eris/eris** (install via `composer require --dev eris/eris`) and run a minimum of 100 iterations each
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation before moving to the next layer
- The `PersonnelForm` Livewire Form Object is the single source of truth for inline validation; `StorePersonnelRequest` / `UpdatePersonnelRequest` are available for any future controller-based routes
