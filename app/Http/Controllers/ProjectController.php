<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    // Show all projects that belong to a specific team
    public function index(Team $team)
    {
        // Check if the user is allowed to access the team
        $this->authorizeTeamAccess($team);
         // Return the list of projects
        return response()->json([
            'projects' => $team->projects
        ]);
    }
    // Create a new project in a specific team
    public function store(Request $request, Team $team)
    {
        // Only team admin or owner can create a project
        $this->authorizeTeamAdmin($team);
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

         // Create the project associated with the team
        $project = $team->projects()->create($validated);

        // Return success response
        return response()->json([
            'message' => 'Project created successfully',
            'project' => $project
        ], 201);
    }
     // Show details of a specific project from a team
    public function show(Team $team, Project $project)
    {
         // Check access rights
        $this->authorizeTeamAccess($team);
          // Ensure the project belongs to the team
        if ($project->team_id !== $team->id) {
            return response()->json(['message' => 'Project not found in this team'], 404);
        }
        // Return project data
        return response()->json(['project' => $project]);
    }
      // Update an existing project in a team
    public function update(Request $request, Team $team, Project $project)
    {
         // Only admin or owner can update a project
        $this->authorizeTeamAdmin($team);

         // Check if the project belongs to the team
        if ($project->team_id !== $team->id) {
            return response()->json(['message' => 'Project not found in this team'], 404);
        }

        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Update the project
        $project->update($validated);

        // Return success response
        return response()->json(['message' => 'Project updated successfully', 'project' => $project]);
    }
    // Delete a project from a team
    public function destroy(Team $team, Project $project)
    {
        // Only admin or owner can delete a project
        $this->authorizeTeamAdmin($team);
        // Check if the project belongs to the team
        if ($project->team_id !== $team->id) {
            return response()->json(['message' => 'Project not found in this team'], 404);
        }
        // Delete the project
        $project->delete();
        // Return success response
        return response()->json(['message' => 'Project deleted successfully']);
    }
    // Helper function to check if user is a team member
    private function authorizeTeamAccess(Team $team)
    {
        $user = Auth::user();
        if (!$team->members->contains($user)) {
            abort(403, 'Unauthorized');
        }
    }
     // Helper function to check if user is team admin or owner
    private function authorizeTeamAdmin(Team $team)
    {
        $user = Auth::user();
        $isOwner = $team->owner_id === $user->id;
        $isAdmin = $team->members()->where('user_id', $user->id)->where('role', 'admin')->exists();
        // Abort if the user is not owner or admin
        if (!$isOwner && !$isAdmin) {
            abort(403, 'Unauthorized');
        }
    }
}
