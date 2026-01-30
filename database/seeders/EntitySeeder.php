<?php

namespace Database\Seeders;

use App\Models\Entity;
use App\Models\User;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        Entity::firstOrCreate(
            ['slug' => 'system'],
            [
                'name' => 'System',
                'type' => 'system',
                'user_id' => $user?->id ?? 1, // Fallback to 1 or handle null appropriately if no user exists
            ]
        );
    }
}
