<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $perPage = $request->input('per_page', 10);
        $tasks = Task::where('colaborator_id', $user->id)->paginate($perPage);
        
        return response()->json($tasks, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string|max:255',
            'colaborator_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id'
        ]);

        $task = Task::create($validated);
        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $task = null;

        if ($user->hasRole('admin')) {
            $task = Task::find($id);
        } else if ($user->hasRole('manager')) {
            $projectIds = Project::where(['manager_id' => $user->id])->pluck('id')->unique();
            $task = Task::whereIn('project_id', $projectIds)->where('id', $id)->first();
            if(!$task){
                $task = Task::where(['id' => $id, 'colaborator_id' => $user->id])->first();
            }
        } else {
            $task = Task::where(['id' => $id, 'colaborator_id' => $user->id])->first();
        }

        return $task !== null ? response()->json($task, 200) : response()->json(['messgage' => 'Task not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'string',
            'description' => 'string|max:255',
            'colaborator_id' => 'exists:users,id'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $task = null;

        if ($user->hasRole('admin')) {
            $task = Task::find($id);
        } else {
            $projectIds = Project::where(['manager_id' => $user->id])->pluck('id')->unique();
            $task = Task::whereIn('project_id', $projectIds)->where('id', $id)->first();
        }

        if (!$task) {
            return response()->json(['messgage' => 'Task not found'], 404);
        }
        
        $task->update($validated);
        return response()->json($task, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $task = null;

        if ($user->hasRole('admin')) {
            $task = Task::find($id);
        } else {
            $projectIds = Project::where(['manager_id' => $user->id])->pluck('id')->unique();
            $task = Task::whereIn('project_id', $projectIds)->where('id', $id)->first();
        }

        if (!$task) {
            return response()->json(['messgage' => 'Task not found'], 404);
        }

        $task->delete();
        return response(null, 204);
    }
}
