# Contributing Guide — Hired Flow

Reference guide for adding new features or modifying existing ones in an organized and consistent way, aligned with the project's architecture.

---

## Layered Architecture

```
app/
├── Actions/              # Atomic business operations (one action = one file)
├── Concerns/             # Reusable traits shared across layers
├── Http/
│   ├── Controllers/      # HTTP controllers (pages, REST endpoints)
│   └── Requests/         # Form Requests (HTTP input validation)
├── Livewire/             # UI components (state and validation only)
├── Models/               # Eloquent models (data + casts + relations)
├── Repositories/         # Direct database access
├── Services/             # Business rule orchestrators
└── View/Components/      # Layout Blade Components
```

---

## When to change each layer

### `Models/`
**Change when:**
- Adding or removing a column from `$fillable`
- Adding a cast (`$casts`)
- Defining a new relation (`hasMany`, `belongsTo`, etc.)
- Adding a Query scope

**Do not put here:**
- Business logic
- Complex queries
- Calls to other services

**Main file:** `app/Models/Application.php`

---

### `database/migrations/`
**Change when:**
- Adding or removing columns from an existing table
- Creating a new table
- Changing an ENUM

**Rule:** always use `Schema::hasColumn()` as a guard before `addColumn` in migrations that alter existing tables — this ensures the migration can be re-run safely on legacy environments.

```php
if (!Schema::hasColumn('applications', 'new_column')) {
    $table->string('new_column')->nullable();
}
```

---

### `Concerns/DetectsApplicationColumns.php`
**Change when:**
- Adding a new optional column to the `applications` table
- Needing to check a column's existence in more than one place (Action, Service, Livewire)

**Example for a new `priority` column:**
```php
public function hasPriorityColumn(): bool
{
    return Schema::hasColumn('applications', 'priority');
}
```

---

### `Actions/`
**Create a new file when:**
- You need to perform **one well-defined, atomic operation** on a model
- The operation is reusable across different contexts (Livewire, Controller, Job, etc.)

**Naming convention:** verb + entity → `CreateApplication`, `ArchiveApplication`, `SendInterviewReminder`

**Standard structure:**
```php
class MyAction
{
    use DetectsApplicationColumns; // if schema guards are needed

    public function execute(Application $application, array $data): void
    {
        // does exactly one thing
    }
}
```

**Register in the service** if it will be called by `ApplicationService`.

---

### `Repositories/ApplicationRepository.php`
**Change when:**
- You need a new database query
- The query is used in more than one place

**Do not put here:**
- Business logic
- Complex conditional rules
- Flash messages

**Example:**
```php
public function findByInterviewDate(Carbon $date, int $userId): Collection
{
    return Application::where('user_id', $userId)
        ->whereDate('interview_date', $date)
        ->get();
}
```

---

### `Services/ApplicationService.php`
**Change when:**
- Orchestrating multiple Actions or Repositories in sequence
- Adding cross-cutting logic (cache, logging, notifications) alongside an operation

**Do not put here:**
- Direct database queries (use the Repository)
- UI code (Livewire/blade)
- Form validation

**Structure when adding a new method:**
```php
public function archiveManually(Application $application): void
{
    $this->archiveAction->execute($application);
    // log, event, notification if needed
}
```

---

### `Livewire/ApplicationsBoard.php`
**Change when:**
- Adding or removing public form fields (`public $field`)
- Adding new validation rules
- Changing UI state (modals, filters, sections)
- Wiring a new `ApplicationService` method to the interface

**Do not put here:**
- Database queries
- Business rules
- Archiving, movement, or other domain logic

**Pattern when adding a new UI action:**
```php
public function myAction(int $id): void
{
    $application = $this->service->findForUser($id, Auth::id());

    if (!$application) {
        return;
    }

    $this->service->myAction($application);

    session()->flash('status', 'Action completed successfully.');
}
```

---

### `Http/Controllers/`
**Change when:**
- Adding a standard HTTP route (settings, pages, API)
- Processing a POST form outside of Livewire

**Main file:** `app/Http/Controllers/SettingsController.php`

---

### `routes/web.php`
**Change when:**
- Adding a new page or endpoint
- Registering a new route for an existing Controller

**All authenticated routes live inside the `middleware('auth')` group.**

---

## Typical flow for a new feature

Example: adding a `priority` field to job applications.

```
1. migration        → add `priority` column to the `applications` table
2. Model            → add `priority` to $fillable and $casts
3. Concerns         → add hasPriorityColumn() to the trait
4. Action           → update CreateApplication and UpdateApplication to include the field
5. Service          → no changes needed (Actions handle it)
6. Livewire         → add public $priority, validation rule, and field binding in the modal
7. Blade            → add input to create-modal and edit-modal, display on the card
8. Seeder           → update ApplicationSeeder with the new fake field
```

---

## Branch and commit conventions

| Type | Prefix | Example |
|------|--------|---------|
| New feature | `feat/` | `feat/add-priority-field` |
| Refactor | `refactor/` | `refactor/clean-functions` |
| Bug fix | `fix/` | `fix/archive-date-boundary` |
| Configuration | `chore/` | `chore/update-dependencies` |

**Commit messages:**
```
feat: add priority field to application cards
fix: correct archive threshold from <= to <
refactor: extract interview scheduling into ScheduleInterview action
chore: update ApplicationSeeder with priority field
```

---

## Useful day-to-day commands

```bash
# Start containers
./vendor/bin/sail up -d

# Run migrations
sail artisan migrate

# Seed the database with fake data
sail artisan db:seed

# Seed only applications
sail artisan db:seed --class=ApplicationSeeder

# Build assets (production)
sail npm run build

# Develop with hot reload
sail npm run dev

# Clear caches
sail artisan config:clear
sail artisan view:clear
sail artisan cache:clear
```
