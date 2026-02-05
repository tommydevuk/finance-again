<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{
    public function show(User $user): Response
    {
        return Inertia::render('Users/Profile', [
            'profile' => new UserResource($user->load('roles')),
        ]);
    }
}
