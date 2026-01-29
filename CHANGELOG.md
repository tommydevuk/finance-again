# Changelog

This file tracks all changes made to the project based on user requests.

## [Unreleased]

### Added
- Created `CHANGELOG.md` to track project history.
- Created `CurrencySeeder` to populate major fiat (USD, EUR, etc.) and crypto (BTC, ETH, etc.) currencies.
- Created `NetworkSeeder` to populate major blockchain networks (Bitcoin, Ethereum, BSC, etc.).
- Updated `DatabaseSeeder` to include the new seeders.
- **[Feature: User Role Assignment CLI]**
    - Optimized Request: Create a CLI command to assign roles to users by ID, restricted to local development, with flags to skip confirmation prompts.
    - Implemented: Created `app/Console/Commands/AssignRoleToUser.php` providing `user:assign-role {user_id} {role_name} {entity_id?}`.
    - Implemented: Added environment check to restrict execution to `local`.
    - Implemented: Added `--force` (or `-F`) flag to bypass interaction.
- **[Feature: System Entity]**
    - Optimized Request: Add a "System" entity via seeder and update the role assignment command to automatically use it as a default permissions boundary.
    - Implemented: Created `EntitySeeder` to generate a default "System" entity (`slug: system`).
    - Implemented: Updated `DatabaseSeeder` to include `EntitySeeder`.
    - Implemented: Updated `user:assign-role` command to automatically resolve and use the "System" entity ID if `config('permission.teams')` is active and no `entity_id` is provided.
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

### Changed
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
