<?php

use App\Enums\CurrencyTypeEnum;
use App\Models\Currency;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;

uses(TestCase::class, RefreshDatabase::class);

test('super admin can manage currencies', function () {
    // Setup Admin
    $entity = \App\Models\Entity::factory()->create();
    app(PermissionRegistrar::class)->setPermissionsTeamId($entity->id);

    $adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web', 'entity_id' => $entity->id]);
    $permission = Permission::firstOrCreate(['name' => 'viewAny currency', 'guard_name' => 'web']);
    $adminRole->givePermissionTo($permission);
    
    $adminUser = User::factory()->create(['name' => 'Admin User']);
    $adminUser->assignRole($adminRole);
    $this->actingAs($adminUser);
    
    // Disable CSRF
    $this->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

    // Test Create
    $response = $this->post(route('system.currencies.store'), [
        'name' => 'Bitcoin',
        'code' => 'BTC',
        'symbol' => '₿',
        'type' => CurrencyTypeEnum::CRYPTO->value,
        'decimals' => 8,
    ]);
    
    $response->assertRedirect(route('system.currencies.index'));
    $this->assertDatabaseHas('currencies', ['code' => 'BTC']);
    
    $currency = Currency::where('code', 'BTC')->first();
    
    // Test Update
    $response = $this->put(route('system.currencies.update', $currency), [
        'name' => 'Bitcoin Updated',
        'code' => 'BTC',
        'symbol' => '₿',
        'type' => CurrencyTypeEnum::CRYPTO->value,
        'decimals' => 8,
    ]);
    
    $response->assertRedirect(route('system.currencies.index'));
    $this->assertDatabaseHas('currencies', ['name' => 'Bitcoin Updated']);
    
    // Test Index
    $this->get(route('system.currencies.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('System/Currencies/Index')
            ->has('currencies.data', 1)
        );
        
    // Test Delete
    $response = $this->delete(route('system.currencies.destroy', $currency));
    $response->assertRedirect(route('system.currencies.index'));
    $this->assertDatabaseMissing('currencies', ['id' => $currency->id]);
});

test('can search and filter currencies', function () {
    // Setup Admin
    $entity = \App\Models\Entity::factory()->create();
    app(PermissionRegistrar::class)->setPermissionsTeamId($entity->id);

    $adminRole = Role::create(['name' => 'Super Admin', 'guard_name' => 'web', 'entity_id' => $entity->id]);
    $permission = Permission::firstOrCreate(['name' => 'viewAny currency', 'guard_name' => 'web']);
    $adminRole->givePermissionTo($permission);
    
    $adminUser = User::factory()->create(['name' => 'Admin User']);
    $adminUser->assignRole($adminRole);
    $this->actingAs($adminUser);
    
    // Create currencies
    Currency::create(['name' => 'US Dollar', 'code' => 'USD', 'type' => 'fiat', 'decimals' => 2]);
    Currency::create(['name' => 'Euro', 'code' => 'EUR', 'type' => 'fiat', 'decimals' => 2]);
    Currency::create(['name' => 'Ethereum', 'code' => 'ETH', 'type' => 'crypto', 'decimals' => 18]);

    // Search by Code
    $this->get(route('system.currencies.index', ['search' => 'ETH']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('currencies.data', 1)
            ->where('currencies.data.0.code', 'ETH')
        );

    // Filter by Type
    $this->get(route('system.currencies.index', ['type' => 'fiat']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('currencies.data', 2) // USD and EUR
        );
});
