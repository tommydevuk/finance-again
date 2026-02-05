<?php

namespace Tests\Feature;

use App\Models\Entity;
use App\Models\EntityInvitation;
use App\Models\User;
use App\Notifications\EntityInvitationCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EntityInvitationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_user_can_invite_member_to_entity()
    {
        Notification::fake();

        $user = User::factory()->create();
        $entity = Entity::factory()->create(['user_id' => $user->id]);
        
        // Grant permission to user on entity
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($entity->id);
        $user->givePermissionTo('update entity'); 

        $response = $this->actingAs($user)
            ->post(route('teams.invitations.store', $entity), [
                'email' => 'test@example.com',
                'role' => 'editor',
            ]);

        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('entity_invitations', [
            'email' => 'test@example.com',
            'entity_id' => $entity->id,
            'role' => 'editor',
        ]);

        Notification::assertSentTo(
            new \Illuminate\Notifications\AnonymousNotifiable,
            EntityInvitationCreated::class,
            function ($notification, $channels, $notifiable) {
                 return $notifiable->routes['mail'] === 'test@example.com';
            }
        );
    }

    public function test_user_can_accept_invitation()
    {
        $inviter = User::factory()->create();
        $entity = Entity::factory()->create(['user_id' => $inviter->id]);
        
        // Create role for testing
        Role::create(['name' => 'editor', 'guard_name' => 'web']);

        $invitation = EntityInvitation::create([
            'email' => 'invitee@example.com',
            'entity_id' => $entity->id,
            'inviter_id' => $inviter->id,
            'role' => 'editor',
            'token' => 'random_token',
            'expires_at' => now()->addDays(7),
        ]);

        $invitee = User::factory()->create(['email' => 'invitee@example.com']);

        $response = $this->actingAs($invitee)
            ->get(route('invitations.accept', 'random_token'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertNotNull($invitation->fresh()->accepted_at);
        
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($entity->id);
        $this->assertTrue($invitee->hasRole('editor'));
    }

    public function test_invitation_is_accepted_after_login()
    {
        $inviter = User::factory()->create();
        $entity = Entity::factory()->create(['user_id' => $inviter->id]);
        
        // Ensure role exists (if not created in previous test or setUp)
        if (Role::where('name', 'editor')->doesntExist()) {
             Role::create(['name' => 'editor', 'guard_name' => 'web']);
        }

        $invitation = EntityInvitation::create([
            'email' => 'newuser@example.com',
            'entity_id' => $entity->id,
            'inviter_id' => $inviter->id,
            'role' => 'editor',
            'token' => 'pending_token',
            'expires_at' => now()->addDays(7),
        ]);

        // Simulate user clicking link (not logged in)
        $response = $this->get(route('invitations.accept', 'pending_token'));
        $response->assertRedirect(route('register', ['email' => 'newuser@example.com']));
        $response->assertSessionHas('invitation_token', 'pending_token');

        // Now simulate Login event
        $newUser = User::factory()->create(['email' => 'newuser@example.com']);
        
        // Mock session state for the event
        session(['invitation_token' => 'pending_token']);

        event(new \Illuminate\Auth\Events\Login('web', $newUser, false));

        $this->assertNotNull($invitation->fresh()->accepted_at);
        
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($entity->id);
        $this->assertTrue($newUser->hasRole('editor'));
    }
}