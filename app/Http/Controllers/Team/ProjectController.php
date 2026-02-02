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
use App\Http\Resources\TaskResource;
use App\Models\User;

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

    /**
     * Show the form for creating a new project.
     */
    public function create(Entity $entity): Response
    {
        Gate::authorize('create', Project::class);

        return Inertia::render('Teams/Projects/Create', [
            'entity' => $entity,
        ]);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Entity $entity, Request $request): \Illuminate\Http\RedirectResponse
    {
        Gate::authorize('create', Project::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $project = $entity->projects()->create($validated);

        // Assign the creator as an 'admin' of the project
        $project->users()->attach($request->user()->id, ['role' => 'admin']);

        // Log activity for the project feed
        activity()
            ->performedOn($project)
            ->causedBy($request->user())
            ->log('Created the project');

        // Log activity for the team feed
        activity()
            ->performedOn($entity)
            ->causedBy($request->user())
            ->log("Created project [project:{$project->uuid}|{$project->name}]");

        return redirect()->route('teams.projects.index', $entity->uuid)
            ->with('success', 'Project created successfully.');
    }

    /**
     * Show the member management page for a project.
     */
    public function editUsers(Entity $entity, Project $project): Response
    {
        Gate::authorize('update', $project);

        // Get all users in the team
        $teamUsers = User::whereIn('id', function ($query) use ($entity) {
            $query->select('model_id')
                ->from(config('permission.table_names.model_has_roles'))
                ->where('entity_id', $entity->id)
                ->where('model_type', User::class);
        })->get();

        return Inertia::render('Teams/Projects/ManageUsers', [
            'entity' => $entity,
            'project' => $project->load('users'),
            'teamUsers' => $teamUsers,
            'availableRoles' => ['member', 'editor', 'admin'],
        ]);
    }

    /**
     * Update project user assignments.
     */
    /**
     * Display the specified project.
     */
    public function show(Entity $entity, Project $project): Response
    {
        Gate::authorize('view', $project);

        $activities = \Spatie\Activitylog\Models\Activity::forSubject($project)
            ->with('causer')
            ->latest()
            ->limit(20)
            ->get();

        $tasks = $project->tasks()
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Teams/Projects/Show', [
            'entity' => $entity,
            'project' => $project->load('users'),
            'activities' => $activities,
            'tasks' => TaskResource::collection($tasks),
        ]);
    }
}
