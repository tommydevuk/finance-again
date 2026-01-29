# Project Overview & Context

## Project Goal
To build a robust, multi-tenant financial and cryptocurrency portfolio tracking application. The system is designed to handle complex transaction types (including crypto gas fees and cross-currency conversions) with high data integrity and precision.

## Core Architecture

### 1. Multi-Tenancy & RBAC
- **Structure:** Users do not "own" accounts directly via a foreign key. Instead, the system uses a **Many-to-Many** relationship (`account_user` pivot).
- **Permissions:** Implementation uses **Spatie Laravel Permission** in **Teams Mode**.
    - **Team:** An `Account` acts as a Team (`team_foreign_key` = `account_id`).
    - **Roles:** Users have scoped roles per account (e.g., 'Admin' on Account A, 'Viewer' on Account B).

### 2. Financial Ledger
- **Transactions:** The `transactions` table tracks individual account movements.
    - **Single-Entry Records:** A transfer between two internal accounts creates **two** transaction records (one negative, one positive).
    - **Direction:** Determined by the sign of the `amount` column.
    - **Precision:** `decimal(36, 18)` is used for all amounts and balances to support ETH/Wei precision.
    - **Fees:** Explicit `fee` and `fee_currency_id` columns track costs separately from the principal.
    - **Metadata:** A `meta_data` JSON column stores platform-specific details (block hashes, nonces, raw API responses).

### 3. Currency & Networks
- **Conversions:** Handled via a dedicated `conversions` table that links a "Source Transaction" (Sell) and a "Destination Transaction" (Buy) with an explicit exchange rate.
- **Networks:** Explicit support for Blockchain Networks (Ethereum, BSC, etc.) via the `networks` table.

## AI Assistant Pre-Prompt (Context Injection)

*Copy and paste the following block to give an AI assistant full context of the project state:*

```text
You are working on a Laravel 11 financial portfolio application with the following architectural constraints:

1.  **Multi-Tenancy:** The app uses Spatie Permissions in "Teams" mode. An `Account` is a "Team". Users belong to accounts via an `account_user` pivot, but their roles are defined in `model_has_roles` scoped by `account_id`.
2.  **Database Schema:**
    -   `accounts`: No `user_id`. Has `balance` (decimal 36,18).
    -   `transactions`: Linked to `account_id`. Uses signed `amount` (decimal 36,18) for direction. Has `meta_data` (JSON) and explicit `fee` columns.
    -   `conversions`: Links two transactions (source/dest) to represent a trade.
    -   `currencies` / `networks` / `platforms`: Reference tables.
3.  **Conventions:**
    -   Always use `decimal(36, 18)` for money/crypto.
    -   Transfers must create two transaction records (Sender: -X, Recipient: +Y).
    -   Conversions must create a `Conversion` record linking the two legs.
    -   Use Spatie's `setPermissionsTeamId($accountId)` before checking roles.

Current State: The database schema is hardened (indexes, precision) and seeded with major currencies/networks.
```
