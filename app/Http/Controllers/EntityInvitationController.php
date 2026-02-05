<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntityInvitationRequest;
use App\Models\Entity;
use App\Models\EntityInvitation;
use App\Models\User;
use App\Notifications\EntityInvitationCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class EntityInvitationController extends Controller
{
    public function store(StoreEntityInvitationRequest $request, Entity $entity)
    {
        $validated = $request->validated();

        $existingUser = User::where('email', $validated['email'])->first();

        if ($existingUser) {
            // Check if user is already a member of this entity
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($entity->id);
            
            // Check if user has any roles on this team
            if ($existingUser->roles->isNotEmpty()) {
                return back()->withErrors(['email' => 'This user is already a member of this team.']);
            }
        }
        
        // Check for existing pending invitation
        $existingInvitation = $entity->invitations()
            ->where('email', $validated['email'])
            ->whereNull('accepted_at')
            ->first();

        if ($existingInvitation) {
             return back()->withErrors(['email' => 'An active invitation already exists for this email.']);
        }

        $invitation = $entity->invitations()->create([
            'email' => $validated['email'],
            'role' => $validated['role'],
            'project_id' => $validated['project_id'] ?? null,
            'inviter_id' => $request->user()->id,
            'token' => Str::random(40),
            'expires_at' => now()->addDays(7),
        ]);

        Notification::route('mail', $validated['email'])->notify(new EntityInvitationCreated($invitation));

        return back()->with('success', 'Invitation sent successfully.');
    }

    public function accept(Request $request, $token)
    {
        $invitation = EntityInvitation::where('token', $token)
                        ->whereNull('accepted_at')
                        ->where(function ($query) {
                            $query->whereNull('expires_at')
                                  ->orWhere('expires_at', '>', now());
                        })
                        ->firstOrFail();

        if (Auth::check()) {
            $user = Auth::user();

            if ($user->email !== $invitation->email) {
                 return redirect()->route('dashboard')->with('error', 'This invitation was sent to a different email address.');
            }

            $this->processAcceptance($user, $invitation);

            return redirect()->route('dashboard')->with('success', 'Invitation accepted. You have joined the team.');
        }

        // Store token in session for registration/login
        session(['invitation_token' => $token]);

        return redirect()->route('register', ['email' => $invitation->email]);
    }

    protected function processAcceptance(User $user, EntityInvitation $invitation)
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($invitation->entity_id);
        
        // We assume the role exists. If using global roles, make sure they are seeded.
        // If not, this might throw RoleDoesNotExist exception.
        // Ideally we should check if role exists, or create it.
        // For now, we assume valid role names are passed.
        $user->assignRole($invitation->role);

        if ($invitation->project_id) {
            $invitation->project->users()->attach($user->id, ['role' => $invitation->role]);
        }

        $invitation->update(['accepted_at' => now()]);
    }
}