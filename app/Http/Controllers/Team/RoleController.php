<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Entity $entity): Response
    {
        Gate::authorize('viewAny permission');

        $roles = Role::where('entity_id', $entity->id)
            ->withCount('permissions')
            ->get();

        return Inertia::render('Teams/Roles/Index', [
            'entity' => $entity,
            'roles' => $roles,
        ]);
    }

    public function editPermissions(Entity $entity, Role $role): Response
    {
        Gate::authorize('update permission');

        // Ensure the role belongs to the entity
        if ($role->entity_id !== $entity->id) {
            abort(403);
        }

        $permissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);
            return count($parts) > 1 ? $parts[1] : 'general';
        });

        return Inertia::render('Teams/Roles/EditPermissions', [
            'entity' => $entity,
            'role' => $role,
            'assigned_permissions' => $role->permissions->pluck('id'),
            'permissions' => $permissions,
        ]);
    }

    public function updatePermissions(Request $request, Entity $entity, Role $role)
    {
        Gate::authorize('update permission');

        if ($role->entity_id !== $entity->id) {
            abort(403);
        }

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->syncPermissions($request->permissions);

        return redirect()->route('teams.roles.index', $entity->uuid)
            ->with('success', 'Role permissions updated successfully.');
    }
}
