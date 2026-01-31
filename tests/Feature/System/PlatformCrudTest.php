<?php

use App\Models\Platform;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

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
