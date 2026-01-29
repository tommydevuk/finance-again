<?php

namespace App\Policies;

use App\Models\Currency;
use App\Models\User;

class CurrencyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('viewAny currency');
    }

    public function view(User $user, Currency $currency): bool
    {
        return $user->hasPermissionTo('view currency');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create currency');
    }

    public function update(User $user, Currency $currency): bool
    {
        return $user->hasPermissionTo('update currency');
    }

    public function delete(User $user, Currency $currency): bool
    {
        return $user->hasPermissionTo('delete currency');
    }
}