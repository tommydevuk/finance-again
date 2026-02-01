<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

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

        $user1 = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Create the System Entity
        $systemEntity = \App\Models\Entity::updateOrCreate(
            ['slug' => 'system'],
            [
                'name' => 'System',
                'type' => 'system',
                'user_id' => $user2->id,
            ]
        );

        // Create Super Admin role scoped to System entity
        $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate([
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
