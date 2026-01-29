<?php

namespace App\Policies;

use App\Models\User;

class PermissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('viewAny permission');
    }

    public function view(User $user): bool
    {
        return $user->hasPermissionTo('view permission');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create permission');
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('update permission');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('delete permission');
    }
}