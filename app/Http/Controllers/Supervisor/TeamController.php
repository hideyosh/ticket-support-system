<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supervisor = auth()->user();
        $teams = Team::where('supervisor_id', $supervisor->id)
            ->withCount([
                'tickets as completed_ticket_count' => function ($query) {
                    $query->whereIn('status', ['resolved', 'closed']);
                },
            ])
            ->get();
        return view('supervisor.team.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('supervisor.team.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_name' => 'required|string|max:255|unique:teams,team_name',
        ]);

        $validated['supervisor_id'] = request()->user()->id;

        Team::create($validated);

        return redirect()->route('supervisor.teams.index')->with('success', "Team berhasil dibuat.");
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Team $team)
    {
        abort_if($team->supervisor_id !== $request->user()->id, 403);

        $team->load('supervisor');

        $agents = $team->agents()
            ->withCount([
                'assignedTickets as active_ticket_count' => function ($query) {
                    $query->whereNotIn('status', ['resolved', 'closed']);
                },
                'assignedTickets as completed_ticket_count' => function ($query) {
                    $query->whereIn('status', ['resolved', 'closed']);
                },
            ])
            ->get();

        $availableAgents = User::whereNull('team_id')->where('role_id', Role::where('role_name', 'agent')->value('id'))->get();

        return view('supervisor.team.show', compact('team', 'agents', 'availableAgents'));
    }

    public function addMember(Request $request, Team $team)
    {
        abort_if($team->supervisor_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'agent_id' => 'required|exists:users,id',
        ]);

        $agent = User::whereNull('team_id')->findOrFail($validated['agent_id']);
        $agent->update(['team_id' => $team->id]);

        return redirect()->route('supervisor.teams.show', $team->id)->with('success', "Agen berhasil ditambahkan ke tim.");
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
