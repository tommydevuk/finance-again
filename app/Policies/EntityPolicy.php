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
        setPermissionsTeamId($entity->id);
        return $user->hasPermissionTo('view entity');
    }

    public function create(User $user): bool
    {
        // Global check (no team context)
        return $user->hasPermissionTo('create entity');
    }

    public function update(User $user, Entity $entity): bool
    {
        setPermissionsTeamId($entity->id);
        return $user->hasPermissionTo('update entity');
    }

    public function delete(User $user, Entity $entity): bool
    {
        setPermissionsTeamId($entity->id);
        return $user->hasPermissionTo('delete entity');
    }
}
