<?php

use App\Models\User;
use App\Models\Entity;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

uses(TestCase::class, RefreshDatabase::class);

test('user retrieves roles relationship', function () {
    $entity = Entity::factory()->create();
    
    // Set the team id for creation
    app(PermissionRegistrar::class)->setPermissionsTeamId($entity->id);

    $role = Role::create(['name' => 'test-role', 'guard_name' => 'web', 'entity_id' => $entity->id]);
    
    $user = User::factory()->create();
    
    $user->assignRole($role);
    
    // Scenario 1: With Context
    $userWithRoles = User::with('roles')->find($user->id);
    expect($userWithRoles->roles)->not->toBeEmpty();
    expect($userWithRoles->roles->first()->name)->toBe('test-role');
    
    // Scenario 2: Without Context (Global View)
    // The user likely wants to see ALL roles in the System Controller, which might not have a specific entity context set,
    // or specifically wants to see all roles the user has across all entities.
    app(PermissionRegistrar::class)->setPermissionsTeamId(null);
    
    // We expect this to contain the role if we fix the issue. 
    // Currently, Spatie default behavior filters this out if team_id is null.
    
    $userWithoutContext = User::with('allRoles')->find($user->id);
    
    // This expectation will likely fail if the bug is what I think it is
    expect($userWithoutContext->allRoles)->not->toBeEmpty();
    expect($userWithoutContext->allRoles->first()->name)->toBe('test-role');
});
