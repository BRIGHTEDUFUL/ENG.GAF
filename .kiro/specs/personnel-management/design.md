# Design Document: Personnel Management

## Overview

The Personnel Management feature delivers a full CRUD interface for employee records inside a Laravel 13 application. The UI is built with Blade + Livewire for reactive, SPA-like interactions without a full JavaScript framework. Tailwind CSS provides responsive styling. SQLite is the backing database.

The feature follows clean architecture principles:

- **Livewire Component** (`PersonnelManager`) — handles all UI state, user interactions, and delegates to the service layer.
- **Service Layer** (`PersonnelService`) — encapsulates all business logic (create, update, soft-delete, query).
- **Form Request Validation** (`StorePersonnelRequest`, `UpdatePersonnelRequest`) — validates incoming data before it reaches the service.
- **Laravel Policy** (`PersonnelPolicy`) — gates every action based on the authenticated user's role.
- **Eloquent Model** (`Personnel`) — maps to the `personnel` table with scopes, casts, and soft-delete support.

The feature supports two roles:
- **Admin** — full CRUD access.
- **Viewer** (any authenticated non-admin user) — read-only access.

---

## Architecture

### Layer Diagram

```
Browser / Livewire Wire Calls
        │
        ▼
┌─────────────────────────────────┐
│  Livewire Component             │
│  App\Livewire\PersonnelManager  │
│  - UI state (search, filters,   │
│    pagination, modal open/close)│
│  - Delegates to PersonnelService│
│  - Authorizes via Policy        │
└────────────┬────────────────────┘
             │ calls
             ▼
┌─────────────────────────────────┐
│  Service Layer                  │
│  App\Services\PersonnelService  │
│  - list(), create(), update(),  │
│    delete(), find()             │
│  - Returns Eloquent models /    │
│    LengthAwarePaginator         │
└────────────┬────────────────────┘
             │ uses
             ▼
┌─────────────────────────────────┐
│  Eloquent Model                 │
│  App\Models\Personnel           │
│  - Scopes: search, inDepartment,│
│    withStatus, active           │
│  - SoftDeletes                  │
│  - Casts: hire_date → Carbon    │
└────────────┬────────────────────┘
             │ persists to
             ▼
        SQLite (personnel table)
```

### Authorization Flow

```
HTTP Request / Livewire Action
        │
        ▼
  Auth Middleware (redirect to login if unauthenticated)
        │
        ▼
  PersonnelPolicy::before() / method-level gate check
        │
   ┌────┴────┐
   │ allowed │  denied → 403 / Livewire error
   ▼
  Component / Service proceeds
```

### Request Lifecycle (Create Example)

```
User submits form
  → Livewire calls save()
  → PersonnelManager resolves StorePersonnelRequest (or validates inline)
  → PersonnelPolicy::create() checked via $this->authorize()
  → PersonnelService::create(array $data) called
  → Personnel::create($data) persisted
  → Success flash dispatched, modal closed, list refreshed
```

---

## Components and Interfaces

### 1. Livewire Component — `App\Livewire\PersonnelManager`

Handles the full personnel management UI as a single Livewire component with internal state for listing, creating, editing, and deleting.

**Public Properties (reactive)**

| Property | Type | Purpose |
|---|---|---|
| `$search` | `string` | Search term, wired to text input |
| `$departmentFilter` | `string` | Selected department filter |
| `$statusFilter` | `string` | Selected status filter (`''`, `'active'`, `'inactive'`) |
| `$showModal` | `bool` | Controls create/edit modal visibility |
| `$showDeleteConfirm` | `bool` | Controls delete confirmation dialog |
| `$editingId` | `?int` | `null` = creating, non-null = editing |
| `$form` | `PersonnelForm` | Livewire Form Object holding field values |

**Methods**

| Method | Description |
|---|---|
| `render()` | Returns paginated personnel list via `PersonnelService::list()` |
| `openCreate()` | Resets form, sets `$editingId = null`, opens modal |
| `openEdit(int $id)` | Loads record into form, sets `$editingId`, opens modal |
| `save()` | Validates form, calls `create()` or `update()` on service, closes modal |
| `confirmDelete(int $id)` | Sets pending delete ID, opens confirmation dialog |
| `delete()` | Calls `PersonnelService::delete()`, closes dialog |
| `updatedSearch()` | Resets pagination on search change |
| `updatedDepartmentFilter()` | Resets pagination on filter change |
| `updatedStatusFilter()` | Resets pagination on filter change |

**Livewire Form Object — `App\Livewire\Forms\PersonnelForm`**

Encapsulates form field state and validation rules, keeping the main component lean.

| Field | Validation Rules |
|---|---|
| `first_name` | `required\|string\|max:100` |
| `last_name` | `required\|string\|max:100` |
| `email` | `required\|email\|max:255\|unique:personnel,email[,{id}]` |
| `phone` | `nullable\|string\|max:20` |
| `department` | `required\|string\|max:100` |
| `position` | `required\|string\|max:100` |
| `hire_date` | `required\|date\|before_or_equal:today` |
| `status` | `required\|in:active,inactive` |
| `avatar` | `nullable\|string` |
| `notes` | `nullable\|string` |

### 2. Service Layer — `App\Services\PersonnelService`

Pure PHP class with no HTTP or Livewire dependencies. Injected into the Livewire component via the constructor.

```php
interface PersonnelServiceInterface
{
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): Personnel;
    public function create(array $data): Personnel;
    public function update(Personnel $personnel, array $data): Personnel;
    public function delete(Personnel $personnel): bool;
    public function departments(): array;
}
```

**`list(array $filters, int $perPage = 15)`**
- Accepts `search`, `department`, `status` keys in `$filters`.
- Chains `scopeSearch`, `scopeInDepartment`, `scopeWithStatus` as needed.
- Returns a `LengthAwarePaginator` with 15 records per page.

**`find(int $id)`**
- Returns the `Personnel` model or throws `ModelNotFoundException` (→ 404).

**`create(array $data)`**
- Calls `Personnel::create($data)`.
- Returns the newly created model.

**`update(Personnel $personnel, array $data)`**
- Calls `$personnel->update($data)`.
- Returns the updated model.

**`delete(Personnel $personnel)`**
- Calls `$personnel->delete()` (soft-delete via `SoftDeletes` trait).
- Returns `true` on success.

**`departments()`**
- Returns a distinct sorted list of department strings from the `personnel` table.
- Used to populate the department filter dropdown.

### 3. Form Request Validation

Two Form Request classes handle HTTP-level validation (used if traditional controller routes are added alongside Livewire). The Livewire Form Object mirrors these rules for inline validation.

**`App\Http\Requests\StorePersonnelRequest`**
- All rules from Requirement 3 Criterion 3.
- `email` uniqueness: `unique:personnel,email`.

**`App\Http\Requests\UpdatePersonnelRequest`**
- Same rules as `StorePersonnelRequest`.
- `email` uniqueness ignores current record: `unique:personnel,email,{$this->route('personnel')->id}`.

### 4. Policy — `App\Policies\PersonnelPolicy`

Registered in `AppServiceProvider` (or auto-discovered).

| Method | Admin | Viewer | Unauthenticated |
|---|---|---|---|
| `viewAny` | ✅ | ✅ | redirect to login |
| `view` | ✅ | ✅ | redirect to login |
| `create` | ✅ | ❌ 403 | redirect to login |
| `update` | ✅ | ❌ 403 | redirect to login |
| `delete` | ✅ | ❌ 403 | redirect to login |

Role check: `$user->role === 'admin'` (or equivalent attribute/method on the `User` model).

### 5. Eloquent Model — `App\Models\Personnel`

Already exists. Key characteristics:

- `SoftDeletes` trait — soft-delete column `deleted_at`.
- `$fillable` — all writable fields listed.
- `casts()` — `hire_date` cast to `date` (Carbon), `status` cast to `string`.
- `getFullNameAttribute()` — computed `full_name` accessor.
- Scopes: `scopeSearch`, `scopeInDepartment`, `scopeWithStatus`, `scopeActive`.

### 6. Routes

```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/personnel', PersonnelManager::class)->name('personnel.index');
    // Optional: dedicated show page
    Route::get('/personnel/{personnel}', PersonnelShow::class)->name('personnel.show');
});
```

All mutation actions (create, update, delete) are handled as Livewire component actions, not separate HTTP routes.

### 7. Blade Views

| View | Purpose |
|---|---|
| `resources/views/livewire/personnel-manager.blade.php` | Main list + modal + delete confirm |
| `resources/views/livewire/personnel-show.blade.php` | Read-only detail view (optional) |

---

## Data Models

### `personnel` Table

| Column | Type | Constraints |
|---|---|---|
| `id` | `BIGINT UNSIGNED` | PK, auto-increment |
| `first_name` | `VARCHAR(100)` | NOT NULL |
| `last_name` | `VARCHAR(100)` | NOT NULL |
| `email` | `VARCHAR(255)` | NOT NULL, UNIQUE |
| `phone` | `VARCHAR(20)` | NULLABLE |
| `department` | `VARCHAR(100)` | NOT NULL |
| `position` | `VARCHAR(100)` | NOT NULL |
| `hire_date` | `DATE` | NOT NULL |
| `status` | `ENUM('active','inactive')` | NOT NULL, DEFAULT `'active'` |
| `avatar` | `VARCHAR(255)` | NULLABLE |
| `notes` | `TEXT` | NULLABLE |
| `created_at` | `TIMESTAMP` | NULLABLE |
| `updated_at` | `TIMESTAMP` | NULLABLE |
| `deleted_at` | `TIMESTAMP` | NULLABLE (soft-delete) |

The migration already exists at `database/migrations/2025_01_01_000010_create_personnel_table.php`.

### `users` Table (existing, extended)

The `User` model needs a `role` column to support the Admin/Viewer distinction.

| Column | Type | Notes |
|---|---|---|
| `role` | `VARCHAR(20)` | `'admin'` or `'viewer'`, default `'viewer'` |

A new migration will add this column:

```php
// database/migrations/YYYY_MM_DD_add_role_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->string('role', 20)->default('viewer')->after('email');
});
```

### Eloquent Relationships

No foreign-key relationships are required for this feature. Personnel records are standalone entities.

### Data Flow Diagram

```
[Browser Form Input]
        │
        ▼
[PersonnelForm (Livewire Form Object)]
  - Validates rules inline
        │
        ▼
[PersonnelService]
  - Calls Eloquent methods
        │
        ▼
[Personnel Eloquent Model]
  - Applies scopes / casts
        │
        ▼
[SQLite: personnel table]
```

---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Search filters are inclusive

*For any* personnel dataset and any non-empty search term, every record returned by `PersonnelService::list(['search' => $term])` SHALL contain the search term (case-insensitively) in at least one of `first_name`, `last_name`, `email`, or `department`.

**Validates: Requirements 2.2**

### Property 2: Department filter is exact

*For any* personnel dataset and any department string, every record returned by `PersonnelService::list(['department' => $dept])` SHALL have a `department` value equal to `$dept`.

**Validates: Requirements 2.3**

### Property 3: Status filter is exact

*For any* personnel dataset and any valid status value (`'active'` or `'inactive'`), every record returned by `PersonnelService::list(['status' => $status])` SHALL have a `status` value equal to `$status`.

**Validates: Requirements 2.4**

### Property 4: Create then retrieve round-trip

*For any* valid personnel data array, calling `PersonnelService::create($data)` followed by `PersonnelService::find($id)` SHALL return a record whose field values match the original `$data`.

**Validates: Requirements 1.1, 3.1**

### Property 5: Soft-delete excludes from list

*For any* personnel record, after `PersonnelService::delete($personnel)` is called, `PersonnelService::list([])` SHALL not include that record in its results, and the record's `deleted_at` SHALL be set to a non-null timestamp.

**Validates: Requirements 6.1, 6.2**

### Property 6: Invalid data is rejected without side effects

*For any* data array that violates at least one validation rule (whitespace-only names, strings exceeding field max lengths, future `hire_date`, invalid `status` value, or duplicate `email`), validation SHALL fail and the total count of personnel records in the database SHALL remain unchanged.

**Validates: Requirements 3.2, 3.3, 5.2**

### Property 7: Viewer cannot mutate

*For any* authenticated user whose `role` is not `'admin'` and any personnel record, `PersonnelPolicy::create()`, `PersonnelPolicy::update()`, and `PersonnelPolicy::delete()` SHALL all return `false`.

**Validates: Requirements 3.4, 5.4, 6.3, 7.2**

### Property 8: All authenticated users can view

*For any* authenticated user regardless of role, `PersonnelPolicy::viewAny()` and `PersonnelPolicy::view()` SHALL return `true`.

**Validates: Requirements 7.1**

### Property 9: Update preserves unmodified fields

*For any* personnel record and any valid partial update array that specifies only a subset of fields, `PersonnelService::update($personnel, $data)` SHALL update the specified fields to their new values and leave all other fields with their original values.

**Validates: Requirements 5.1**

---

## Error Handling

### 404 — Record Not Found

- `PersonnelService::find(int $id)` throws `Illuminate\Database\Eloquent\ModelNotFoundException`.
- Laravel's exception handler converts this to a 404 HTTP response automatically.
- In Livewire context, the component catches this and dispatches a user-visible error notification.

### 403 — Unauthorized Action

- `$this->authorize('create', Personnel::class)` (and similar) in the Livewire component throws `Illuminate\Auth\Access\AuthorizationException`.
- Laravel converts this to a 403 HTTP response.
- In Livewire context, the component catches this and shows an "Access denied" notification.

### Validation Errors

- `PersonnelForm` uses Livewire's built-in `validate()` which throws `ValidationException` and populates `$errors` automatically.
- Field-level error messages are displayed inline next to each form input.
- No data is persisted when validation fails.

### Duplicate Email

- The `unique:personnel,email` rule in the form object catches duplicate emails at validation time, before any database write.
- The update rule uses `unique:personnel,email,{$id}` to ignore the current record's own email.

### Unauthenticated Access

- All personnel routes are wrapped in the `auth` middleware.
- Unauthenticated requests are redirected to the login page (Laravel default behavior).

### Soft-Delete Safety

- Soft-deleted records are excluded from all queries by default (Eloquent `SoftDeletes` trait).
- Attempting to find a soft-deleted record by ID via `find()` returns null / throws `ModelNotFoundException`.

---

## Testing Strategy

### Unit Tests (PHPUnit)

Target the service layer and policy in isolation, using an in-memory SQLite database (already configured).

**`PersonnelServiceTest`**
- `list()` returns paginated results (15 per page).
- `list(['search' => ...])` filters correctly.
- `list(['department' => ...])` filters correctly.
- `list(['status' => ...])` filters correctly.
- `create()` persists a record and returns the model.
- `update()` modifies only the supplied fields.
- `delete()` soft-deletes the record (sets `deleted_at`).
- `find()` throws `ModelNotFoundException` for missing/deleted records.
- `departments()` returns a sorted, distinct list.

**`PersonnelPolicyTest`**
- Admin can `viewAny`, `view`, `create`, `update`, `delete`.
- Viewer can `viewAny` and `view` but not `create`, `update`, `delete`.

**`PersonnelFormValidationTest`**
- Valid data passes all rules.
- Empty `first_name` / `last_name` fails.
- Whitespace-only `first_name` / `last_name` fails.
- Invalid email format fails.
- Duplicate email fails (unique rule).
- `hire_date` in the future fails.
- `status` outside `active|inactive` fails.
- `phone` exceeding 20 characters fails.

### Property-Based Tests

The project uses PHPUnit. For property-based testing, we will use **[eris/eris](https://github.com/giorgiosironi/eris)** — a property-based testing library for PHP that integrates with PHPUnit.

Each property test runs a minimum of **100 iterations**.

Tag format in comments: `Feature: personnel-management, Property {N}: {property_text}`

**Property 1 — Search filters are inclusive**
Generate random personnel datasets and random non-empty search terms. Assert every result contains the term (case-insensitively) in `first_name`, `last_name`, `email`, or `department`.

**Property 2 — Department filter is exact**
Generate random personnel datasets and random department strings. Assert every result's `department` equals the filter value exactly.

**Property 3 — Status filter is exact**
Generate random personnel datasets with mixed statuses. For each status value, assert every result's `status` equals the filter value.

**Property 4 — Create then retrieve round-trip**
Generate random valid personnel data arrays. Assert `find(create($data)->id)` returns a record whose fields match `$data`.

**Property 5 — Soft-delete excludes from list**
Generate random personnel records. After `delete()`, assert the record does not appear in `list([])` and `deleted_at` is non-null.

**Property 6 — Invalid data is rejected without side effects**
Generate data arrays violating at least one rule (whitespace-only names, oversized strings, future dates, invalid status, duplicate email). Assert validation fails and the record count is unchanged.

**Property 7 — Viewer cannot mutate**
Generate random non-admin users. Assert `PersonnelPolicy::create()`, `update()`, and `delete()` all return `false`.

**Property 8 — All authenticated users can view**
Generate random users with any role. Assert `PersonnelPolicy::viewAny()` and `view()` return `true`.

**Property 9 — Update preserves unmodified fields**
Generate random personnel records and random partial update arrays (subset of fields). Assert updated fields match new values and all other fields remain unchanged.

### Feature / Integration Tests (Livewire)

Use `Livewire::test(PersonnelManager::class)` to test the full component lifecycle.

- Authenticated user sees paginated list.
- Admin can open create modal, submit valid form, see success notification.
- Admin can open edit modal, submit valid form, see success notification.
- Admin can confirm delete, see record removed from list.
- Viewer sees list but cannot open create/edit/delete actions (403).
- Unauthenticated user is redirected to login.
- Search input filters the displayed list.
- Department and status filters narrow the list.
- Empty state message shown when no records match.
