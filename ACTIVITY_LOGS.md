# Activity Log Architecture

This document outlines the locations, subjects, and display areas for all activity logs within the system, powered by `spatie/laravel-activitylog`.

## Log Summary

| Subject Model | Trigger Type | Log Name | Logged Attributes | Parent / Feed Location |
| :--- | :--- | :--- | :--- | :--- |
| **Entity** (Team) | Automatic | `team` | `name` | **Team Overview** (`Teams/Show.vue`) |
| **Project** | Automatic | `project` | `name`, `description` | **Project Overview** (`Teams/Projects/Show.vue`) |
| **Entity** (Team) | Manual | `default` | Project Metadata | **Team Overview** (`Teams/Show.vue`) |

---

## Detailed Logging Definitions

### 1. Team Activities (Entity)
*   **Location**: `app/Models/Entity.php`
*   **Automatic Tracking**: Logs whenever the team name is updated.
*   **Manual Tracking**: 
    *   **Project Creation**: Logged in `Team\ProjectController@store`. When a project is created, a log is attached to the **Entity** so it appears in the high-level team feed.
*   **Display**: These logs are fetched in `TeamController@show` and displayed in the right-hand column of the Team Overview dashboard.

### 2. Project Activities
*   **Location**: `app/Models/Project.php`
*   **Automatic Tracking**: Logs whenever the project `name` or `description` is modified.
*   **Scope**: Logs are attached to the specific **Project** instance.
*   **Display**: These logs are fetched in `Team\ProjectController@show` and displayed in the main activity column of the individual Project Overview screen.

## Technical Notes

### Scoping
- **Team-level visibility**: Actions that affect the entire team (creating projects, changing team names) are logged against the `Entity` model.
- **Project-level visibility**: Actions scoped to a specific project (updating project details) are logged against the `Project` model.

### Implementation Pattern
- **Trait**: Most logging is handled via the `LogsActivity` trait on models.
- **Helper**: Manual logs use the `activity()` helper to point events to parent entities (e.g., pointing a "Project Created" event to the "Team" subject).
