<?php

namespace App\Http\Controllers\System;

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
    public function index(): Response
    {
        Gate::authorize('viewAny permission');

        $systemEntity = Entity::where('type', 'system')->first();

        $roles = Role::where('entity_id', $systemEntity?->id)
            ->withCount('permissions')
            ->get();

        return Inertia::render('System/Roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function editPermissions(Role $role): Response
    {
        Gate::authorize('update permission');

        $permissions = Permission::all()->groupBy(function ($permission) {
            // Group by model name (e.g., 'viewAny network' -> 'network')
            $parts = explode(' ', $permission->name);

            return count($parts) > 1 ? $parts[1] : 'general';
        });

        return Inertia::render('System/Roles/EditPermissions', [
            'role' => $role,
            'assigned_permissions' => $role->permissions->pluck('id'),
            'permissions' => $permissions,
        ]);
    }

    public function updatePermissions(Request $request, Role $role)
    {
        Gate::authorize('update permission');

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if (config('permission.teams') && isset($role->entity_id)) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($role->entity_id);
        }

        $role->syncPermissions($request->permissions);

        return redirect()->route('system.roles.index')
            ->with('success', 'Permissions updated successfully.');
    }
}
