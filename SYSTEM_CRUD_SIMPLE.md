# System CRUD Simple - Implementation Guide

This guide outlines the steps to create a simple CRUD for a new model within the System administration area, following the patterns established in the `Platform` CRUD.

## 1. Prerequisites
- Ensure the migration for the model exists and includes standard fields (e.g., `name`, `slug`, `created_at`, `updated_at`).
- Ensure the model has a corresponding Factory for testing.

## 2. Backend Implementation

### 2.1. Domain Query
Create a query class in `app/Domain/[ModelPlural]/Query/System[Model]Query.php`.
- Extend or implement a base query pattern if available.
- Implement `filter(Request $request)` to handle:
  - `search`: Filter by name or other text fields.
  - `sort` & `direction`: Handle sorting.
  - Any specific filters (e.g., `type`).

### 2.2. Request Validation
Create `app/Http/Requests/System/[Model]Request.php`.
- Authorize: `return $this->user()->can('viewAny', [Model]::class);`
- Rules: Validate fields. Use Enums for fixed types. Handle unique slug generation if applicable.

### 2.3. Resource
Create `app/Http/Resources/[Model]Resource.php`.
- Transform the model into a JSON array for the frontend.

### 2.4. Controller
Create `app/Http/Controllers/System/[Model]Controller.php`.
- **index**: Use Query class to filter/sort. Return `Inertia::render` with resource collection, filters, and any necessary options (e.g., Enums for dropdowns).
- **create**: Return `Inertia::render` with options.
- **store**: Validate, create model (generate slug if needed), redirect with success message.
- **edit**: Return `Inertia::render` with model resource and options.
- **update**: Validate, update model, redirect.
- **destroy**: Delete model, redirect.

### 2.5. Routes
Update `routes/web.php` in the `system` prefix group.
- `Route::resource('[models]', \App\Http\Controllers\System\[Model]Controller::class);`

## 3. Frontend Implementation

### 3.1. List Page (Index.vue)
Create `resources/js/pages/System/[Models]/Index.vue`.
- Use `useQueryFilters` composable.
- Use reusable components: `DataSearch`, `DataSort`.
- Implement `DropdownMenu` for custom filters.
- Display data in `ResourceGrid` / `Card`.
- Include `Pagination`.

### 3.2. Create/Edit Pages
Create `resources/js/pages/System/[Models]/Create.vue` and `Edit.vue`.
- Use `useForm` from Inertia.
- Use `AppLayout`, `PageHeader`.
- Form fields: `Input`, `Select` (native or custom), `InputError`.
- `Edit.vue`: Include Delete action (via `Dialog` confirmation).

## 4. Testing
Create `tests/Feature/System/[Model]CrudTest.php`.
- Use `RefreshDatabase`.
- Setup Super Admin user with necessary Entity context and Permissions.
- Test:
  - Create (store).
  - Update.
  - Index (search/filter).
  - Delete.
- **Note:** Disable CSRF middleware in test if encountering 419 errors (`$this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);`).
- **Note:** Ensure environment variables `DB_CONNECTION` and `DB_DATABASE` are correctly set to use the test database.

## 5. Enum (Optional but Recommended)
If the model has a status or type field:
- Create `app/Enums/[Field]Enum.php`.
- Use `label()` method to return display names.
- Use `cases()` mapped to `{ label, value }` in Controller to pass to Frontend.

## 6. Build
Run `npm run build` to ensure all frontend assets are compiled and type-checked.
