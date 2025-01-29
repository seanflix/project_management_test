<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $user->belongsToTeam($project->team);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $project): bool
    {
        return $user->belongsToTeam($project->team);
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->belongsToTeam($project->team) && 
            $user->hasTeamPermission($project->team, 'delete');
    }
}
