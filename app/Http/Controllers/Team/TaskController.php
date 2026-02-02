<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Entity;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    /**
     * Store a newly created task.
     */
    public function store(Entity $entity, Project $project, Request $request)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:tasks,id',
            'priority' => 'required|string|in:low,medium,high',
            'status' => 'required|string|in:todo,in_progress,done',
        ]);

        $maxSort = Task::where('project_id', $project->id)
            ->where('parent_id', $validated['parent_id'])
            ->max('sort_order');

        $task = $project->tasks()->create([
            ...$validated,
            'sort_order' => $maxSort + 1,
        ]);

        activity()
            ->performedOn($project)
            ->causedBy(auth()->user())
            ->event('task_created')
            ->log("Created task [task:{$task->uuid}|{$task->name}]");

        return back()->with('success', 'Task created successfully.');
    }

    /**
     * Update the specified task.
     */
    public function update(Entity $entity, Project $project, Task $task, Request $request)
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:todo,in_progress,done',
            'priority' => 'required|string|in:low,medium,high',
        ]);

        $before = $task->only(['name', 'description', 'status', 'priority']);

        $task->update($validated);

        $after = $task->fresh()->only(['name', 'description', 'status', 'priority']);

        // Log status change separately
        if ($before['status'] !== $after['status']) {
            activity()
                ->performedOn($project)
                ->causedBy(auth()->user())
                ->event('task_status_changed')
                ->log("Changed status of [task:{$task->uuid}|{$task->name}] to '{$after['status']}'");
        }

        // Log other field changes
        $otherFieldsChanged = false;
        $fieldsToCompare = ['name', 'description', 'priority'];

        foreach ($fieldsToCompare as $field) {
            if ($before[$field] !== $after[$field]) {
                $otherFieldsChanged = true;
                break;
            }
        }

        if ($otherFieldsChanged) {
            activity()
                ->performedOn($project)
                ->causedBy(auth()->user())
                ->event('task_updated')
                ->log("Updated [task:{$task->uuid}|{$task->name}]");
        }

        return back()->with('success', 'Task updated successfully.');
    }

    /**
     * Reorder tasks (and potentially move to subtask).
     */
    public function reorder(Entity $entity, Project $project, Request $request)
    {
        Gate::authorize('update', $project);

        $request->validate([
            'tasks' => 'required|array',
            'tasks.*.id' => 'required|exists:tasks,id',
            'tasks.*.sort_order' => 'required|integer',
            'tasks.*.parent_id' => 'nullable|exists:tasks,id',
        ]);

        foreach ($request->tasks as $taskData) {
            Task::where('id', $taskData['id'])->update([
                'sort_order' => $taskData['sort_order'],
                'parent_id' => $taskData['parent_id'],
            ]);
        }

        return back()->with('success', 'Task order updated.');
    }

    /**
     * Remove the specified task.
     */
    public function destroy(Entity $entity, Project $project, Task $task)
    {
        Gate::authorize('update', $project);

        $task->delete();

        return back()->with('success', 'Task deleted.');
    }
}
