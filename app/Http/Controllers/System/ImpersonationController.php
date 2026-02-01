<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ImpersonationController extends Controller
{
    public function store(User $user)
    {
        Gate::authorize('impersonate user');

        // Prevent impersonating yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        // Prevent impersonating other super admins if needed, but for now allow it as per "login as any"
        
        // Log the current user out
        // Optional: We could store the original user ID in session to allow "stop impersonating"
        // But the requirement is just "login as user button".
        
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "You are now impersonating {$user->name}.");
    }
}