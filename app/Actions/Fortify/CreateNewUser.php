<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

use App\Models\Entity;
use Illuminate\Support\Str;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);

        // Create Personal Team (Entity)
        $entity = Entity::create([
            'name' => $input['name'] . "'s Team",
            'slug' => Str::slug($input['name'] . "'s Team") . '-' . Str::random(6),
            'type' => 'company',
            'user_id' => $user->id,
        ]);

        // Create Admin Role for this entity
        $adminRole = \Spatie\Permission\Models\Role::create([
            'name' => \App\Enums\RolesEnum::ADMIN->value,
            'guard_name' => 'web',
            'entity_id' => $entity->id,
        ]);

        // Assign Permissions (all except impersonation)
        $permissions = \Spatie\Permission\Models\Permission::where('name', '!=', 'impersonate user')->get();
        $adminRole->syncPermissions($permissions);

        // Assign User to Admin Role
        setPermissionsTeamId($entity->id);
        $user->assignRole($adminRole);
        setPermissionsTeamId(null);

        return $user;
    }
}
