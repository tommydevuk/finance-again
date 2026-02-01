<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class UserDashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $entities = \App\Models\Entity::whereIn('id', function ($query) use ($user) {
            $query->select(config('permission.table_names.model_has_roles') . '.entity_id')
                ->from(config('permission.table_names.model_has_roles'))
                ->where('model_id', $user->id)
                ->where('model_type', $user->getMorphClass())
                ->join(config('permission.table_names.roles'), 'roles.id', '=', 'model_has_roles.role_id')
                ->where('roles.name', \App\Enums\RolesEnum::ADMIN->value);
        })->get();

        return Inertia::render('Users/Dashboard', [
            'entities' => $entities,
        ]);
    }
}
