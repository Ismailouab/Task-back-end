<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // Get all tasks for a specific project in a team
    public function index(Team $team, Project $project)
    {

        $this->authorizeTeamAccess($team, $project);

        return response()->json([
            'tasks' => $project->tasks
        ]);
    }
    // Create a new task in a specific project of a team
    public function store(Request $request, Team $team, Project $project)
    {
        // Check if the user has access
        $this->authorizeTeamAccess($team, $project);

          // Validate task data from the request
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,in_progress,completed',
            'priority' => 'in:low,medium,high',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        // Create the task in the project
        $task = $project->tasks()->create($validated);

        // Return success response with the created task
        return response()->json([
            'message' => 'Task created successfully',
            'task' => $task
        ], 201);
    }

    // Update an existing task in a project
    public function update(Request $request, Team $team, Project $project, Task $task)
    {
        // Check access permissions
        $this->authorizeTeamAccess($team, $project);

         // Ensure the task belongs to the specified project
        if ($task->project_id !== $project->id) {
            return response()->json(['message' => 'Task not found in this project'], 404);
        }
        // Validate the incoming data (all fields are optional here)
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:pending,in_progress,completed',
            'priority' => 'in:low,medium,high',
            'due_date' => 'nullable|date|after_or_equal:today',

        ]);
        // Update the task with validated data
        $task->update($validated);

        // Return updated task info
        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task
        ]);
    }
    // Delete a task from a project
    public function destroy(Team $team, Project $project, Task $task)
    {
        // Check user access
        $this->authorizeTeamAccess($team, $project);

        // Make sure the task is part of the given project
        if ($task->project_id !== $project->id) {
            return response()->json(['message' => 'Task not found in this project'], 404);
        }

         // Delete the task
        $task->delete();

        // Return success response
        return response()->json(['message' => 'Task deleted successfully']);
    }
    // Check if the user is part of the team and if the project belongs to the team
    private function authorizeTeamAccess(Team $team, Project $project)
    {
        $user = Auth::user();
         // User must be a team member and project must belong to the team
        if (!$team->members->contains($user) || $project->team_id !== $team->id) {
            abort(403, 'Unauthorized');
        }
    }
}