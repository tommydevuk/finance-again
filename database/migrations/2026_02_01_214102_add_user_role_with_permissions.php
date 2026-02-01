<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Ensure permission exists
        $permission = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'create entity', 'guard_name' => 'web']);

        // Create User role (global, no entity_id)
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => \App\Enums\RolesEnum::USER->value, 'guard_name' => 'web']);
        
        // Assign permission
        $role->givePermissionTo($permission);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We generally don't delete roles/permissions in down() to avoid accidental data loss if reused, 
        // but strictly we could revoke.
    }
};