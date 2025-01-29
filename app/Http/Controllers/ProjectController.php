<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Auth::user()->currentTeam->projects()
            ->withCount('tasks')
            ->latest()
            ->get()
            ->map(function ($project) {
                $project->isAuthorized = auth()->user()->can('delete', $project);
                return $project;
            });

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:not_started,in_progress,completed,on_hold'],
            'due_date' => ['required', 'date'],
        ]);

        Auth::user()->currentTeam->projects()->create($validated);

        return redirect()->route('projects.index');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['tasks' => function ($query) {
            $query->latest()->with('user');
        }]);

        $project->isAuthorized = auth()->user()->can('delete', $project);
        $teamUsers = $project->team->users()->get();

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'tasks' => $project->tasks,
            'taskStats' => [
                'total' => $project->tasks->count(),
                'completed' => $project->tasks->where('status', 'completed')->count(),
            ],
            'teamUsers' => $teamUsers,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:not_started,in_progress,completed,on_hold'],
            'due_date' => ['required', 'date'],
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project);
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index');
    }
}
