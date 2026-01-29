# Project Overview & Context

## Project Goal
To build a robust, multi-tenant financial and cryptocurrency portfolio tracking application. The system is designed to handle complex transaction types (including crypto gas fees and cross-currency conversions) with high data integrity and precision.

## Core Architecture

### 1. Multi-Tenancy & RBAC
- **Hierarchy:** `Entity` -> `Accounts` -> `Transactions`.
- **Permissions:** Implementation uses **Spatie Laravel Permission** in **Teams Mode**.
    - **Team:** An `Entity` acts as a Team (`team_foreign_key` = `entity_id`).
    - **Roles:** Users have scoped roles per Entity (e.g., 'Admin' of 'My LLC' or 'Personal').
- **Membership:** Users belong to an `Entity` and have access to its Accounts.

### 2. Financial Ledger
- **Transactions:** The `transactions` table tracks individual account movements.
    - **Single-Entry Records:** A transfer between two internal accounts creates **two** transaction records (one negative, one positive).
    - **Direction:** Determined by the sign of the `amount` column.
    - **Precision:** `decimal(36, 18)` is used for all amounts and balances to support ETH/Wei precision.
    - **Fees:** Explicit `fee` and `fee_currency_id` columns track costs separately from the principal.
    - **Metadata:** A `meta_data` JSON column stores platform-specific details.

### 3. Currency & Networks
- **Conversions:** Handled via a dedicated `conversions` table linking a "Source" and "Destination" transaction.
- **Networks:** Explicit support for Blockchain Networks via `networks` table.

## AI Assistant Pre-Prompt (Context Injection)

*Copy and paste the following block to give an AI assistant full context:*

```text
You are working on a Laravel 11 financial portfolio application.

1.  **Multi-Tenancy:** Spatie Permissions in "Teams" mode. `Entity` is the "Team". Permissions scoped to `entity_id`.
2.  **Schema:**
    -   `entities`: Parent grouping (User owned). Acts as RBAC Team.
    -   `accounts`: Belongs to `Entity`. Has `balance` (decimal 36,18).
    -   `transactions`: Linked to `account_id`. Uses signed `amount` (decimal 36,18). Has `meta_data` (JSON) and `fee` columns.
    -   `conversions`: Links source/dest transactions.
3.  **Conventions:**
    -   Always use `decimal(36, 18)`.
    -   Transfers = 2 transaction records.
    -   Use Spatie's `setPermissionsTeamId($entityId)`.
```
