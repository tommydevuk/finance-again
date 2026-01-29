<?php

namespace App\Policies;

use App\Models\Platform;
use App\Models\User;

class PlatformPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('viewAny platform');
    }

    public function view(User $user, Platform $platform): bool
    {
        return $user->hasPermissionTo('view platform');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create platform');
    }

    public function update(User $user, Platform $platform): bool
    {
        return $user->hasPermissionTo('update platform');
    }

    public function delete(User $user, Platform $platform): bool
    {
        return $user->hasPermissionTo('delete platform');
    }
}