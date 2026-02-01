<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('can filter users by role', function () {
    // Setup Entity and Team Context
    $entity = \App\Models\Entity::factory()->create();
    app(PermissionRegistrar::class)->setPermissionsTeamId($entity->id);

    // Setup roles
    $adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web', 'entity_id' => $entity->id]);
    $editorRole = Role::create(['name' => 'Editor', 'guard_name' => 'web', 'entity_id' => $entity->id]);

    // Create users
    $adminUser = User::factory()->create(['name' => 'Admin User']);
    $adminUser->assignRole($adminRole);

    $editorUser = User::factory()->create(['name' => 'Editor User']);
    $editorUser->assignRole($editorRole);

    $normalUser = User::factory()->create(['name' => 'Normal User']);

    // Set context to null for the actual request simulation if checking "system wide"
    // OR keep it if the controller relies on it.
    // The Controller uses `allRoles` which ignores scope, so filtering should work regardless of current team ID
    // IF the query uses `allRoles` correctly.
    // However, the `UserIndexRequest` calls `can('viewAny', User::class)`.
    // We need to ensure $adminUser has permission to view users.
    // Usually Super Admin bypasses checks or we need to give permission.

    // Let's grant viewAny permission to adminRole
    $permission = \Spatie\Permission\Models\Permission::create(['name' => 'viewAny user', 'guard_name' => 'web']);
    $adminRole->givePermissionTo($permission);

    // Re-login to refresh permissions might be needed, or just actingAs handles it?
    // Spatie caches permissions.
    app(PermissionRegistrar::class)->forgetCachedPermissions();

    // Test filtering by Super Admin
    $this->actingAs($adminUser)
        ->get(route('system.users.index', ['role' => 'Super Admin']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('System/Users/Index')
            ->has('users.data', 1)
            ->where('users.data.0.email', $adminUser->email)
        );

    // Test filtering by Editor
    $this->actingAs($adminUser)
        ->get(route('system.users.index', ['role' => 'Editor']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('System/Users/Index')
            ->has('users.data', 1)
            ->where('users.data.0.email', $editorUser->email)
        );

    // Test filtering by All (empty role)
    $this->actingAs($adminUser)
        ->get(route('system.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('System/Users/Index')
            ->has('users.data', 4)
        );
});
