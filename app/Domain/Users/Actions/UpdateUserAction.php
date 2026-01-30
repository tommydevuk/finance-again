<?php

namespace App\Domain\Users\Actions;

use App\Domain\Users\DTOs\UserData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUserAction
{
    public function execute(User $user, UserData $data): User
    {
        $userData = $data->toArray();

        // If password is not provided, don't update it
        if (empty($userData['password'])) {
            unset($userData['password']);
        } else {
            $userData['password'] = Hash::make($userData['password']);
        }

        $user->update($userData);

        return $user;
    }
}
