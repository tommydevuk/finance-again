<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    /**
     * Show the team dashboard.
     */
    public function show(Entity $entity): Response
    {
        // Set the current team/entity context for permission checks
        setPermissionsTeamId($entity->id);

        // Authorize that the user can view this entity (requires Admin role on this entity)
        Gate::authorize('view', $entity);

        return Inertia::render('Teams/Show', [
            'entity' => $entity,
        ]);
    }
}
