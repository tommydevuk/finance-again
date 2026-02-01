# Changelog

This file tracks all changes made to the project based on user requests.

## [Unreleased]

### Added
- **[Feature: UUIDs & Slugs]**
    - Optimized Request: Add UUIDs to core models (`Entity`, `Account`, `User`, `Transaction`) and Slugs to `Network` and `Platform`.
    - Implemented: Created migration `2026_02_01_175437_add_uuids_and_slugs_to_tables` which adds `uuid` column to specified tables and `slug` to `networks`.
    - Implemented: Migration includes backfill logic to populate UUIDs/Slugs for existing data, ensuring safety in dev/prod environments.
    - Implemented: Updated `User`, `Entity`, `Account`, `Transaction` models to use `HasUuids` trait.
    - Implemented: Updated `Network` model to include `slug` in fillable attributes.
- **[Feature: User Management Enhancements]**
    - Optimized Request: Add context menu to Users index, create View page using UUID, and add Impersonation capability.
    - Implemented: Added `impersonate user` permission and assigned to Super Admin.
    - Implemented: Created `ImpersonationController` to handle "Login as User".
    - Implemented: Updated `System/Users/Index.vue` with Shadcn Dropdown Menu replacing the Edit button.
    - Implemented: Created `System/Users/Show.vue` view page accessed via UUID (`/system/users/{uuid}`).
    - Implemented: Added `show` method to `System/UserController` and updated routes.

### Added
- Created `CHANGELOG.md` to track project history.
- Created `CurrencySeeder` to populate major fiat (USD, EUR, etc.) and crypto (BTC, ETH, etc.) currencies.
- Created `NetworkSeeder` to populate major blockchain networks (Bitcoin, Ethereum, BSC, etc.).
- Updated `DatabaseSeeder` to include the new seeders.
- **[Feature: Schema Hardening]**
    - Optimized Request: Improve schema robustness for crypto applications by increasing precision, adding metadata flexibility, and optimizing query performance.
    - Implemented: Updated `transactions` and `accounts` tables to use `decimal(36, 18)` for `amount`, `amount_native`, and `balance` to support high-precision cryptocurrencies (like ETH/Wei).
    - Implemented: Added `meta_data` JSON column to `transactions` table for flexible storage of platform-specific details.
    - Implemented: Added database indexes to `transactions` table (`type`, `status`, `date`, and composite `[account_id, date]`) to improve query performance.
    - Implemented: Updated `Transaction` model to cast `meta_data` as array and `TransactionFactory` to generate fake metadata.
- **[Feature: Currency Conversions]**
    - Optimized Request: Implement a robust way to handle currency conversions, accounting for rates and linking the "source" and "destination" transactions.
    - Implemented: Created `conversions` table to explicitly link a source transaction (sell) and a destination transaction (buy) with an exchange rate.
    - Implemented: Created `Conversion` model and relationships in `Transaction` model.
- **[Feature: Transaction Fees]**
    - Optimized Request: Handle fees for transactions, including same-currency transfers.
    - Implemented: Added `fee` (decimal) and `fee_currency_id` (foreign key) columns to `transactions` table to explicitly track transaction costs.
    - Implemented: Updated `Transaction` model with `feeCurrency` relationship.
- **[Feature: RBAC & Multi-Tenant Accounts]**
    - Optimized Request: Add Spatie Permissions library and transition to a multi-tenant model where multiple users can have roles at an account via a pivot table.
    - Implemented: Installed `spatie/laravel-permission` and enabled the **Teams** feature.
    - Implemented: Configured `entity_id` as the team foreign key to allow scoped role assignments (multi-tenancy).
    - Implemented: Removed `user_id` from `accounts` table and created the `account_user` pivot table.
    - Implemented: Updated `User` model with `HasRoles` trait and `BelongsToMany` accounts relationship.
- **[Feature: Entity Layer]**
    - Optimized Request: Add a grouping layer (Entity) for accounts, serving as the primary permissions boundary to accommodate both sole traders and companies.
    - Implemented: Created `entities` table (Parent) and `Entity` model.
    - Implemented: Added `entity_id` to `accounts` table.
    - Implemented: Updated Spatie Permission config to use `entity_id` as the Team ID (replacing `account_id`), enabling Entity-level roles.
    - **[Feature: Roles Management Interface]**
        - Optimized Request: Create a roles index with tiled layout and separate routed crud screen for editing permissions.
        - Implemented: Created `System/RoleController` with index, edit, and update actions.
        - Implemented: Created `System/Roles/Index.vue` with a tiled card layout showing role summaries.
        - Implemented: Created `System/Roles/EditPermissions.vue` with a grouped grid of checkboxes for intuitive permission management.
        - Implemented: Added "Roles" navigation to the sidebar and system dashboard.
- **[Feature: Super Admin & System Dashboard]**
    - Optimized Request: Create a "Super Admin" role and a system dashboard accessible only to those users.
    - Implemented: Created `RolesAndPermissionsSeeder` and `RolesEnum` enum.
    - Implemented: Added `Gate::before` and `viewSystemDashboard` gates in `AppServiceProvider`.
    - Implemented: Created `SystemController` and `/system` route.
    - Implemented: Created `System/Dashboard.vue` page and added it to the sidebar for Super Admins.
    - **[Correction: System Entity]**
        - Implemented: Reverted Global Role approach in favor of a "System Entity" (Entity with `type='system'`) to house the Super Admin role, ensuring Primary Key integrity in `model_has_roles`.

### Changed
- **[Refactor: Role Permissions Edit Page]**
    - Optimized Request: Ensure data consistency and proper reactivity in the Role Permissions edit component, using IDs for robust identification.
    - Implemented: Refactored `RoleController` to pass `assigned_permissions` as an array of IDs and validate against IDs.
    - Implemented: Refactored `EditPermissions.vue` to use permission IDs for state management and checkbox binding.
- **[Refactor: Transaction Model & Schema]**
    - Optimized Request: Refactor `Transaction` model to use foreign keys (`sender_account_id`, `recipient_account_id`) instead of text fields for data integrity.
    - Implemented: Updated `create_transactions_table` migration to replace `account_id`, `sender_*`, and `recipient_*` text fields with nullable `sender_account_id` and `recipient_account_id` foreign keys referencing `accounts`.
    - Implemented: Updated `Transaction` model with `senderAccount` and `recipientAccount` relationships.
    - Implemented: Updated `Account` model with `sentTransactions` and `receivedTransactions` relationships.
    - Implemented: Updated `TransactionFactory` to use the new foreign keys.
- **[Refactor: Cleanup Migrations]**
    - Merged `add_network_id_to_accounts_table`, `add_network_id_to_transactions_table`, and `add_sender_and_recipient_to_transactions_table` into the original `create_accounts_table` and `create_transactions_table` migrations.
    - Renamed `create_networks_table` to run before account/transaction creation to satisfy foreign key constraints.
    - Removed redundant incremental migration files.
- **[Refactor: Revert to Single Account ID]**
    - Optimized Request: Revert `Transaction` schema to use a single `account_id`. Determine transaction direction (sender/recipient) via positive/negative `amount` and handle transfers by creating a record for each participating account (double-entry).
    - Implemented: Reverted `create_transactions_table` migration to use `account_id` and removed `sender_account_id`/`recipient_account_id`.
    - Implemented: Restored `account()` relationship in `Transaction` model and `transactions()` in `Account` model.
    - Implemented: Updated `TransactionFactory` to use `account_id`.

### Notes
- Interpreted "major bank transaction types" as a request to ensure major Fiat currencies used in banking are present, alongside major cryptocurrencies.
- Used generic string fields for `sender_address` and `recipient_address` to support both IBANs/Bank Account numbers and Crypto Wallet addresses.