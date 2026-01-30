<?php

namespace App\Http\Controllers\System;

use App\Domain\Users\Actions\CreateUserAction;
use App\Domain\Users\Actions\DeleteUserAction;
use App\Domain\Users\Actions\UpdateUserAction;
use App\Domain\Users\DTOs\UserData;
use App\Domain\Users\Query\SystemUserQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\StoreUserRequest;
use App\Http\Requests\System\UpdateUserRequest;
use App\Http\Requests\System\UserIndexRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(UserIndexRequest $request)
    {
        $query = new SystemUserQuery();
        $users = $query->filter($request)->getQuery()->with('roles')->paginate(10)->withQueryString();

        return Inertia::render('System/Users/Index', [
            'users' => UserResource::collection($users),
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }

    public function create()
    {
        Gate::authorize('create', User::class);
        return Inertia::render('System/Users/Create');
    }

    public function store(StoreUserRequest $request, CreateUserAction $action)
    {
        $data = UserData::fromRequest($request);
        $action->execute($data);

        return redirect()->route('system.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        Gate::authorize('update', $user);
        return Inertia::render('System/Users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action)
    {
        $data = UserData::fromRequest($request);
        $action->execute($user, $data);

        return redirect()->route('system.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user, DeleteUserAction $action)
    {
        Gate::authorize('delete', $user);
        $action->execute($user);

        return redirect()->route('system.users.index')->with('success', 'User deleted successfully.');
    }
}
