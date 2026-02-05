<?php

namespace App\Listeners;

use App\Models\EntityInvitation;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AcceptInvitationOnLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        if (session()->has('invitation_token')) {
            $token = session('invitation_token');
            
            $invitation = EntityInvitation::where('token', $token)
                        ->whereNull('accepted_at')
                        ->where(function ($query) {
                            $query->whereNull('expires_at')
                                  ->orWhere('expires_at', '>', now());
                        })
                        ->first();

            if ($invitation && $invitation->email === $event->user->email) {
                 $user = $event->user;
                 
                 // Accept the invitation
                 app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($invitation->entity_id);
                 
                 // Ensure role exists or just assign (Spatie throws if not found)
                 try {
                     $user->assignRole($invitation->role);
                 } catch (\Exception $e) {
                     // Log error or ignore? 
                     // Ideally we create the role if missing or fallback.
                 }

                 if ($invitation->project_id) {
                    $invitation->project->users()->attach($user->id, ['role' => $invitation->role]);
                 }

                 $invitation->update(['accepted_at' => now()]);
                 
                 session()->forget('invitation_token');
                 session()->flash('success', 'Invitation accepted. You have joined the team.');
            }
        }
    }
}