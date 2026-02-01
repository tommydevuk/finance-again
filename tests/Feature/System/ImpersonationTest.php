<?php

namespace Tests\Feature\System;

use App\Models\Entity;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

class ImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_impersonate_user()
    {
        $entity = Entity::factory()->create(['type' => 'system']);
        app(PermissionRegistrar::class)->setPermissionsTeamId($entity->id);

        $adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web', 'entity_id' => $entity->id]);
        $permission = Permission::firstOrCreate(['name' => 'impersonate user', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permission);

        $admin = User::factory()->create(['name' => 'Admin User']);
        $admin->assignRole($adminRole);

        $targetUser = User::factory()->create(['name' => 'Target User']);

        $this->actingAs($admin);

        $response = $this->post(route('system.users.impersonate', $targetUser));

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($targetUser);
    }

    public function test_user_cannot_impersonate_without_permission()
    {
        $entity = Entity::factory()->create(['type' => 'system']);
        app(PermissionRegistrar::class)->setPermissionsTeamId($entity->id);

        $user = User::factory()->create(['name' => 'Regular User']);
        $targetUser = User::factory()->create(['name' => 'Target User']);

        $this->actingAs($user);

        $response = $this->post(route('system.users.impersonate', $targetUser));

        $response->assertForbidden();
    }

    public function test_cannot_impersonate_self()
    {
        $entity = Entity::factory()->create(['type' => 'system']);
        app(PermissionRegistrar::class)->setPermissionsTeamId($entity->id);

        $adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web', 'entity_id' => $entity->id]);
        $permission = Permission::firstOrCreate(['name' => 'impersonate user', 'guard_name' => 'web']);
        $adminRole->givePermissionTo($permission);

        $admin = User::factory()->create(['name' => 'Admin User']);
        $admin->assignRole($adminRole);

        $this->actingAs($admin);

        $response = $this->post(route('system.users.impersonate', $admin));

        $response->assertRedirect();
        $response->assertSessionHas('error', 'You cannot impersonate yourself.');
        $this->assertAuthenticatedAs($admin);
    }
}
