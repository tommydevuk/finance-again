<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Entity $entity, Request $request): Response
    {
        Gate::authorize('viewAny user');

        $users = User::whereIn('id', function ($query) use ($entity) {
            $query->select('model_id')
                ->from(config('permission.table_names.model_has_roles'))
                ->where('entity_id', $entity->id)
                ->where('model_type', User::class);
        })
        ->with(['roles' => function ($query) use ($entity) {
            $query->where('entity_id', $entity->id);
        }])
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->paginate(12)
        ->withQueryString();

        return Inertia::render('Teams/Users/Index', [
            'entity' => $entity,
            'users' => $users,
            'filters' => $request->only(['search', 'sort', 'direction']),
        ]);
    }
}
