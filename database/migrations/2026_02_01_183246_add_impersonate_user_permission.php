<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Enums\RolesEnum;
use App\Models\Entity;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create permission
        $permission = Permission::firstOrCreate(['name' => 'impersonate user', 'guard_name' => 'web']);

        // Assign to Super Admin
        // We need to find the Super Admin role. It might be global or attached to the System Entity.
        // Based on previous context, Super Admin role is attached to a "System Entity".
        
        // Let's try to find the role broadly.
        $roles = Role::where('name', RolesEnum::SUPER_ADMIN->value)->get();
        
        foreach ($roles as $role) {
            $role->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permission = Permission::where('name', 'impersonate user')->first();
        if ($permission) {
            $permission->delete();
        }
    }
};