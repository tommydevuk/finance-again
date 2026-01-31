<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SystemController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewSystemDashboard');

        return Inertia::render('System/Dashboard', [
            'counts' => [
                'users' => \App\Models\User::count(),
                'roles' => \Spatie\Permission\Models\Role::count(),
                'entities' => \App\Models\Entity::count(),
                'transactions' => \App\Models\Transaction::count(),
                'platforms' => \App\Models\Platform::count(),
                'currencies' => \App\Models\Currency::count(),
            ],
        ]);
    }
}
