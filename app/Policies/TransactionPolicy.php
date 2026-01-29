<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('viewAny transaction');
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $user->hasPermissionTo('view transaction');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create transaction');
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->hasPermissionTo('update transaction');
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->hasPermissionTo('delete transaction');
    }
}