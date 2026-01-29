<?php

namespace App\Domain\Users\Actions;

use App\Models\User;

class DeleteUserAction
{
    public function execute(User $user): bool
    {
        return $user->delete();
    }
}
