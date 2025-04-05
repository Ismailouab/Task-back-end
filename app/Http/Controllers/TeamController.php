<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    // Get all teams for the current user
    public function index()
{
    $user = Auth::user();
    $allTeams = $user->teams->merge($user->ownedTeams)->unique('id');

    return response()->json([
        'teams' => $allTeams,
        'owned_teams' => $user->ownedTeams
    ]);
}
    // Create a new team
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_public' => 'boolean'
        ]);

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => Auth::id(),
            'is_public' => $request->is_public ?? false
        ]);

        // Add creator as owner
        $team->addMember(Auth::user(), 'owner');

        return response()->json([
            'message' => 'Team created successfully',
            'team' => $team
        ], 201);
    }

    // Get team details
    public function show(Team $team)
    {
        // Check if user can view the team
        if (!$team->is_public && !$team->members->contains(Auth::user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'team' => $team->load(['owner', 'members'])
        ]);
    }

    // Update team
    public function update(Request $request, Team $team)
    {
        // Check if user can update the team (owner or admin)
        $isOwner = $team->owner_id === Auth::id();
        $isAdmin = $team->members()
            ->where('user_id', Auth::id())
            ->where('role', 'admin')
            ->exists();

        if (!$isOwner && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:100',
            'description' => 'nullable|string',
            'is_public' => 'sometimes|boolean'
        ]);

        $team->update($request->only(['name', 'description', 'is_public']));

        return response()->json([
            'message' => 'Team updated successfully',
            'team' => $team
        ]);
    }

    // Delete team
    public function destroy(Team $team)
    {
        // Only owner can delete team
        if ($team->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $team->delete();

        return response()->json([
            'message' => 'Team deleted successfully'
        ]);
    }
    // In your TeamController or a new controller
public function getNonMembers(Team $team)
{
    // Check if user can view potential members (owner or admin)
    $isOwner = $team->owner_id === Auth::id();
    $isAdmin = $team->members()
        ->where('user_id', Auth::id())
        ->where('role', 'admin')
        ->exists();

    if (!$isOwner && !$isAdmin) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $currentMemberIds = $team->members()->pluck('users.id');
    
    $users = User::whereNotIn('id', $currentMemberIds)
        ->when(request('search'), function($query, $search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->select('id', 'name', 'email')
        ->paginate(10);

    return response()->json($users);
}

    // Add member to team
    public function addMember(Request $request, Team $team)
    {
        // Check if user can update the team (owner or admin)
        $isOwner = $team->owner_id === Auth::id();
        $isAdmin = $team->members()
            ->where('user_id', Auth::id())
            ->where('role', 'admin')
            ->exists();

        if (!$isOwner && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => ['required', Rule::in(['member', 'admin'])]
        ]);

        $user = User::where('email', $request->email)->first();

        if ($team->hasMember($user)) {
            return response()->json([
                'message' => 'User is already a team member'
            ], 422);
        }

        $team->addMember($user, $request->role);

        return response()->json([
            'message' => 'Member added successfully',
            'team' => $team->load('members')
        ]);
    }

    // Update member role
    public function updateMemberRole(Request $request, Team $team, User $user)
    {
        // Only owner can update roles
        if ($team->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'role' => ['required', Rule::in(['member', 'admin'])]
        ]);

        if (!$team->hasMember($user)) {
            return response()->json([
                'message' => 'User is not a team member'
            ], 404);
        }

        $team->updateMemberRole($user, $request->role);

        return response()->json([
            'message' => 'Member role updated successfully'
        ]);
    }

    // Remove member from team
    public function removeMember(Team $team, User $user)
    {
        // Check if user can update the team (owner or admin)
        $isOwner = $team->owner_id === Auth::id();
        $isAdmin = $team->members()
            ->where('user_id', Auth::id())
            ->where('role', 'admin')
            ->exists();

        if (!$isOwner && !$isAdmin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($team->owner_id === $user->id) {
            return response()->json([
                'message' => 'Cannot remove team owner'
            ], 422);
        }

        if (!$team->hasMember($user)) {
            return response()->json([
                'message' => 'User is not a team member'
            ], 404);
        }

        $team->removeMember($user);

        return response()->json([
            'message' => 'Member removed successfully'
        ]);
    }
}