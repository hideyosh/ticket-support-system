<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
// use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function show(Team $team) {
        $agents = User::where('team_id', $team->id)->paginate(10);
        return view('agent.team.show', compact('team', 'agents'));
    }
}
