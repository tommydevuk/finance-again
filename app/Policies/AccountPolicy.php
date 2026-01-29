<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;

class AccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('viewAny account');
    }

    public function view(User $user, Account $account): bool
    {
        return $user->hasPermissionTo('view account');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create account');
    }

    public function update(User $user, Account $account): bool
    {
        return $user->hasPermissionTo('update account');
    }

    public function delete(User $user, Account $account): bool
    {
        return $user->hasPermissionTo('delete account');
    }
}