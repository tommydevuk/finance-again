<?php

namespace App\Console\Commands;

use App\Models\Entity;
use App\Models\User;
use Illuminate\Console\Command;

class AssignRoleToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-role {user_id} {role_name} {entity_id?} {--F|force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a role to a user by ID (Local environment only)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! app()->isLocal()) {
            $this->error('This command can only be run in the local environment.');

            return 1;
        }

        $userId = $this->argument('user_id');
        $roleName = $this->argument('role_name');
        $entityId = $this->argument('entity_id');

        $user = User::find($userId);

        if (! $user) {
            $this->error("User with ID {$userId} not found.");

            return 1;
        }

        if (config('permission.teams')) {
            if (! $entityId) {
                $systemEntity = Entity::where('slug', 'system')->first();
                if ($systemEntity) {
                    $entityId = $systemEntity->id;
                    $this->info("No entity ID provided. Using System Entity (ID: {$entityId}).");
                } else {
                    $this->error('No entity ID provided and System Entity not found.');

                    return 1;
                }
            }
            setPermissionsTeamId($entityId);
        }

        if (! $this->option('force')) {
            $confirmationMessage = "Are you sure you want to assign role '{$roleName}' to user '{$user->name}' ({$user->email})?";
            if ($entityId) {
                $confirmationMessage .= " for Entity ID {$entityId}?";
            }
            if (! $this->confirm($confirmationMessage)) {
                $this->info('Operation cancelled.');

                return 0;
            }
        }

        try {
            $user->assignRole($roleName);
            $this->info("Role '{$roleName}' assigned to user '{$user->name}' successfully.");
        } catch (\Throwable $e) {
            $this->error('Failed to assign role: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
