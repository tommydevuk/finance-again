<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

use App\Http\Resources\ProjectResource;

class ProjectController extends Controller
{
    /**
     * Display a listing of the team's projects scoped to user access.
     */
    public function index(Entity $entity, Request $request): Response
    {
        Gate::authorize('viewAny', Project::class);

        $user = $request->user();

        $projects = Project::where('entity_id', $entity->id)
            ->where(function ($query) use ($user, $entity) {
                // 1. Team Admins see all projects
                $query->whereExists(function ($q) use ($user, $entity) {
                    $pivotTable = config('permission.table_names.model_has_roles');
                    $q->select(\Illuminate\Support\Facades\DB::raw(1))
                        ->from($pivotTable)
                        ->join(config('permission.table_names.roles'), 'roles.id', '=', $pivotTable . '.role_id')
                        ->where('model_id', $user->id)
                        ->where('model_type', $user->getMorphClass())
                        ->where($pivotTable . '.entity_id', $entity->id)
                        ->where('roles.name', \App\Enums\RolesEnum::ADMIN->value);
                })
                // 2. Others only see projects they are assigned to
                ->orWhereHas('users', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
            ->withCount('users')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Teams/Projects/Index', [
            'entity' => $entity,
            'projects' => ProjectResource::collection($projects),
            'filters' => $request->only(['search']),
        ]);
    }
}
