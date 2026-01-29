<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $user1 = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
        ]);

        // Create the System Entity
        $systemEntity = \App\Models\Entity::factory()->create([
            'name' => 'System',
            'slug' => 'system',
            'type' => 'system',
            'user_id' => $user2->id,
        ]);

        // Create Super Admin role scoped to System entity
        $superAdminRole = \Spatie\Permission\Models\Role::create([
            'name' => \App\Enums\RolesEnum::SUPER_ADMIN->value,
            'guard_name' => 'web',
            'entity_id' => $systemEntity->id,
        ]);

        // Assign all permissions to Super Admin
        $superAdminRole->syncPermissions(\Spatie\Permission\Models\Permission::all());

        // Assign Super Admin role to user 2 in the System entity context
        setPermissionsTeamId($systemEntity->id);
        $user2->assignRole($superAdminRole);
        setPermissionsTeamId(null); // Reset team context

        $this->call([
            EntitySeeder::class,
            CurrencySeeder::class,
            NetworkSeeder::class,
        ]);
    }
}
