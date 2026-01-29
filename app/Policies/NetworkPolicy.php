<?php

namespace App\Policies;

use App\Models\Network;
use App\Models\User;

class NetworkPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('viewAny network');
    }

    public function view(User $user, Network $network): bool
    {
        return $user->hasPermissionTo('view network');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create network');
    }

    public function update(User $user, Network $network): bool
    {
        return $user->hasPermissionTo('update network');
    }

    public function delete(User $user, Network $network): bool
    {
        return $user->hasPermissionTo('delete network');
    }
}