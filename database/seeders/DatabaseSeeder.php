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

        // Assign Super Admin role scoped to the System Entity
        setPermissionsTeamId($systemEntity->id);
        $user2->assignRole('Super Admin');

        $this->call([
            EntitySeeder::class,
            CurrencySeeder::class,
            NetworkSeeder::class,
        ]);
    }
}
