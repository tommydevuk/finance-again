<?php

use App\Models\Platform;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('super admin can manage platforms', function () {
    // Setup Admin
    $entity = \App\Models\Entity::factory()->create();
    app(PermissionRegistrar::class)->setPermissionsTeamId($entity->id);

    $adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web', 'entity_id' => $entity->id]);

    // Grant viewAny permission to adminRole for Platform
    $permission = Permission::create(['name' => 'viewAny platform', 'guard_name' => 'web']);
    $adminRole->givePermissionTo($permission);

    // Just in case Gate::authorize checks specific permissions in future, but currently controller uses viewAny?
    // UserIndexRequest used 'viewAny', PlatformRequest uses 'viewAny'.

    $adminUser = User::factory()->create(['name' => 'Admin User']);
    $adminUser->assignRole($adminRole);

    // Login
    $this->actingAs($adminUser);

    // Disable CSRF
    $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

    // Test Create
    $response = $this->post(route('system.platforms.store'), [
        'name' => 'Test Bank',
        'type' => 'bank',
        'website' => 'https://testbank.com',
    ]);

    $response->assertRedirect(route('system.platforms.index'));
    $this->assertDatabaseHas('platforms', ['name' => 'Test Bank']);

    $platform = Platform::first();

    // Test Update
    $response = $this->put(route('system.platforms.update', $platform), [
        'name' => 'Updated Bank',
        'type' => 'exchange',
        'website' => 'https://updated.com',
    ]);

    $response->assertRedirect(route('system.platforms.index'));
    $this->assertDatabaseHas('platforms', ['name' => 'Updated Bank', 'type' => 'exchange']);

    // Test Index
    $this->get(route('system.platforms.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('System/Platforms/Index')
            ->has('platforms.data', 1)
        );

    // Test Delete
    $response = $this->delete(route('system.platforms.destroy', $platform));
    $response->assertRedirect(route('system.platforms.index'));
    $this->assertDatabaseMissing('platforms', ['id' => $platform->id]);
});

test('can search and filter platforms', function () {
    // Setup Admin
    $entity = \App\Models\Entity::factory()->create();
    app(PermissionRegistrar::class)->setPermissionsTeamId($entity->id);

    $adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web', 'entity_id' => $entity->id]);
    $permission = Permission::firstOrCreate(['name' => 'viewAny platform', 'guard_name' => 'web']);
    $adminRole->givePermissionTo($permission);

    $adminUser = User::factory()->create(['name' => 'Admin User']);
    $adminUser->assignRole($adminRole);
    $this->actingAs($adminUser);

    // Create platforms
    Platform::create(['name' => 'Bank A', 'type' => 'bank', 'website' => 'https://bank-a.com', 'slug' => 'bank-a']);
    Platform::create(['name' => 'Exchange B', 'type' => 'exchange', 'website' => 'https://exchange-b.com', 'slug' => 'exchange-b']);
    Platform::create(['name' => 'Casino C', 'type' => 'casino', 'website' => 'https://casino-c.com', 'slug' => 'casino-c']);

    // Search by Website
    $this->get(route('system.platforms.index', ['search' => 'bank-a.com']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('platforms.data', 1)
            ->where('platforms.data.0.name', 'Bank A')
        );

    // Filter by Type
    $this->get(route('system.platforms.index', ['type' => 'exchange']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('platforms.data', 1)
            ->where('platforms.data.0.name', 'Exchange B')
        );
});
