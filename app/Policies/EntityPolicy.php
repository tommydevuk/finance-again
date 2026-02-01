<?php

namespace App\Policies;

use App\Models\Entity;
use App\Models\User;

class EntityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('viewAny entity');
    }

    public function view(User $user, Entity $entity): bool
    {
        return \Illuminate\Support\Facades\DB::table(config('permission.table_names.model_has_roles'))
            ->join(config('permission.table_names.roles'), 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_id', $user->id)
            ->where('model_type', $user->getMorphClass())
            ->where('entity_id', $entity->id)
            ->where('roles.name', \App\Enums\RolesEnum::ADMIN->value)
            ->exists();
    }

    public function create(User $user): bool
    {
        // Global check for create permission
        return $user->hasPermissionTo('create entity');
    }

    public function update(User $user, Entity $entity): bool
    {
        return \Illuminate\Support\Facades\DB::table(config('permission.table_names.model_has_roles'))
            ->join(config('permission.table_names.roles'), 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_id', $user->id)
            ->where('model_type', $user->getMorphClass())
            ->where('entity_id', $entity->id)
            ->where('roles.name', \App\Enums\RolesEnum::ADMIN->value)
            ->exists();
    }

    public function delete(User $user, Entity $entity): bool
    {
        return \Illuminate\Support\Facades\DB::table(config('permission.table_names.model_has_roles'))
            ->join(config('permission.table_names.roles'), 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_id', $user->id)
            ->where('model_type', $user->getMorphClass())
            ->where('entity_id', $entity->id)
            ->where('roles.name', \App\Enums\RolesEnum::ADMIN->value)
            ->exists();
    }
}
