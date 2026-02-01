<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    /**
     * Show the create team form.
     */
    public function create(): Response
    {
        return Inertia::render('Teams/Create');
    }

    /**
     * Store a new team.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:entities,name',
        ]);

        $user = $request->user();

        // Create Entity
        $entity = Entity::create([
            'name' => $validated['name'],
            'slug' => \Illuminate\Support\Str::slug($validated['name']),
            'type' => 'company',
            'user_id' => $user->id,
        ]);

        // Create Admin Role for this entity
        $adminRole = \Spatie\Permission\Models\Role::create([
            'name' => \App\Enums\RolesEnum::ADMIN->value,
            'guard_name' => 'web',
            'entity_id' => $entity->id,
        ]);

        // Assign Permissions (all except impersonation)
        $permissions = \Spatie\Permission\Models\Permission::where('name', '!=', 'impersonate user')->get();
        $adminRole->syncPermissions($permissions);

        // Assign User to Admin Role
        setPermissionsTeamId($entity->id);
        $user->assignRole($adminRole);
        setPermissionsTeamId(null);

        return to_route('teams.show', $entity->uuid);
    }

    /**
     * Show the team dashboard.
     */
    public function show(Entity $entity): Response
    {
        // Set the current team/entity context for permission checks
        setPermissionsTeamId($entity->id);

        // Authorize that the user can view this entity (requires Admin role on this entity)
        Gate::authorize('view', $entity);

        return Inertia::render('Teams/Show', [
            'entity' => $entity,
        ]);
    }
}
