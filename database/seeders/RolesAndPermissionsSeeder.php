<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define models and their actions
        $models = [
            'network',
            'entity',
            'currency',
            'platform',
            'account',
            'transaction',
            'permission', // Managing permissions themselves
        ];

        $actions = ['viewAny', 'view', 'create', 'update', 'delete'];

        $permissions = [];

        foreach ($models as $model) {
            foreach ($actions as $action) {
                $permissions[] = "{$action} {$model}";
            }
        }

        // Create Permissions (Global scope for simplicity in seeding, though used with teams later)
        // Note: With 'teams' enabled, permissions usually need a team_id unless global.
        // For Super Admin, we often want Global permissions.
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Super Admin Role (Global - no team_id)
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
            'entity_id' => null, // Global Role
        ]);

        // Assign all permissions to Super Admin
        $superAdminRole->syncPermissions(Permission::all());
    }
}