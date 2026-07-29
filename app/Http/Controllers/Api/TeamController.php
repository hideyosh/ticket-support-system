<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Team;

class TeamController extends Controller
{
    public function index()
    {
        return response()->json(
            Team::with('supervisor:id,name')->select('id', 'team_name', 'supervisor_id')->get()
        );
    }
}
