<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with('supervisor')
            ->withCount('agents')
            ->withCount(['tickets as completed_tickets_count' => function($query) {
                $query->whereIn('status', ['closed', 'resolved']);
            }])
            ->orderBy('team_name', 'asc')
            ->paginate(15);

        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('role_name', 'supervisor');
        })->with('role')->get();

        return view('admin.teams.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_name'    => ['required', 'string', 'max:255'],
            'supervisor_id'=> ['nullable', Rule::exists('users', 'id')],
        ]);

        Team::create($validated);

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team berhasil dibuat.');
    }

    public function show(Team $team)
    {
        $team->load(['supervisor', 'agents']);

        return view('admin.teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        $team->load('supervisor');

        $users = User::whereHas('role', function ($query) {
            $query->where('role_name', 'supervisor');
        })->with('role')->get();

        return view('admin.teams.edit', compact('team', 'users'));
    }

    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'team_name'    => ['required', 'string', 'max:255'],
            'supervisor_id'=> ['nullable', Rule::exists('users', 'id')],
        ]);

        $team->update($validated);

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team berhasil diperbarui.');
    }

    public function destroy(Team $team)
    {
        $team->delete();

        return redirect()->route('admin.teams.index')
            ->with('success', 'Team berhasil dihapus.');
    }
}
