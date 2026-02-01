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
        return $user->hasPermissionTo('view entity', $entity->id);
    }

    public function create(User $user): bool
    {
        // Creating an entity is a global action (not scoped to an existing entity)
        return $user->hasPermissionTo('create entity');
    }

    public function update(User $user, Entity $entity): bool
    {
        return $user->hasPermissionTo('update entity', $entity->id);
    }

    public function delete(User $user, Entity $entity): bool
    {
        return $user->hasPermissionTo('delete entity', $entity->id);
    }
}
