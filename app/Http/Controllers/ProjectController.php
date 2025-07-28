<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $perPage = $request->input('per_page', 10);

        if ($user->hasRole('admin')) {
            $projects = Project::paginate($perPage);
        } else if ($user->hasRole('manager')) {
            $projects = Project::where('manager_id', $user->id)->paginate($perPage);
        } else {
            $projectIds = Task::where('colaborator_id', $user->id)->pluck('project_id')->unique();
            $projects = Project::whereIn('id', $projectIds)->paginate($perPage);
        }

        return response()->json($projects, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:projects,name',
            'description' => 'required|string|max:255',
            'manager_id' => 'required|exists:users,id'
        ]);

        $manager = User::find($validated['manager_id']);
        if (!$manager->hasRole(['manager', 'admin'])) {
            return response()->json(['error' => 'User is not a manager'], 422);
        }

        $project = Project::create($validated);
        return response()->json($project, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $project = null;

        if ($user->hasRole('admin')) {
            $project = Project::find($id);
            if ($project) {
                $project->tasks;
            }
        } else if ($user->hasRole('manager')) {
            $project = Project::where(['id' => $id, 'manager_id' => $user->id])->first();
            if (!$project) {
                $hasTask = Task::where([
                    'project_id' => $id,
                    'colaborator_id' => $user->id
                ])->exists();
                if ($hasTask) {
                    $project = Project::find($id);
                }
                return response()->json($project, 200);
            }
            $project->tasks;
        } else {
            $hasTask = Task::where([
                'project_id' => $id,
                'colaborator_id' => $user->id
            ])->exists();
            if ($hasTask) {
                $project = Project::find($id);
            }
        }

        return $project !== null ? response()->json($project, 200) : response()->json(['message' => 'Project not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'string',
            'description' => 'string|max:255',
            'manager_id' => 'exists:users,id'
        ]);

        if (isset($validated['manager_id'])) {
            $manager = User::find($validated['manager_id']);
            if (!$manager->hasRole(['manager', 'admin'])) {
                return response()->json(['error' => 'User is not a manager'], 422);
            }
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $project = null;

        if ($user->hasRole('admin')) {
            $project = Project::find($id);
        } else {
            $project = Project::where(['id' => $id, 'manager_id' => $user->id])->first();
        }

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $project->update($validated);
        return response()->json($project, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $project = Project::find($id);

        if (!$project) {
            return response()->json(['message' => 'Project not found'], 404);
        }

        $project->delete();
        return response(null, 204);
    }
}
