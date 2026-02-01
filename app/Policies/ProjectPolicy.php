<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use App\Enums\RolesEnum;
use Illuminate\Support\Facades\DB;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any projects within the team.
     */
    public function viewAny(User $user): bool
    {
        // Permission to 'viewAny project' must be held globally or in the current team context.
        return $user->hasPermissionTo('viewAny project');
    }

    /**
     * Determine whether the user can view the specific project.
     */
    public function view(User $user, Project $project): bool
    {
        // 1. If user is Admin of the team, they can view all projects in that team
        if ($this->isTeamAdmin($user, $project->entity_id)) {
            return true;
        }

        // 2. Otherwise, check if they are explicitly assigned to this project
        return $project->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create projects.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create project');
    }

    /**
     * Determine whether the user can update the project.
     */
    public function update(User $user, Project $project): bool
    {
        if ($this->isTeamAdmin($user, $project->entity_id)) {
            return true;
        }

        return $project->users()
            ->where('user_id', $user->id)
            ->whereIn('project_user.role', ['editor', 'admin']) // specific project roles
            ->exists();
    }

    /**
     * Determine whether the user can delete the project.
     */
    public function delete(User $user, Project $project): bool
    {
        return $this->isTeamAdmin($user, $project->entity_id);
    }

    /**
     * Helper to check if user is a Team Admin.
     */
    protected function isTeamAdmin(User $user, int $entityId): bool
    {
        return DB::table(config('permission.table_names.model_has_roles'))
            ->join(config('permission.table_names.roles'), 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_id', $user->id)
            ->where('model_type', $user->getMorphClass())
            ->where('entity_id', $entityId)
            ->where('roles.name', RolesEnum::ADMIN->value)
            ->exists();
    }
}