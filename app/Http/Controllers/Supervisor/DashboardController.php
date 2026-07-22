<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\Ticket;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $supervisor = auth()->user();
        $teams = Team::where('supervisor_id', $supervisor->id)
            ->withCount('agents')
            ->with('agents:id,team_id')
            ->get();

        $teamIds = $teams->pluck('id');

        $statusCounts = Ticket::join('users', 'tickets.assigned_to', '=', 'users.id')
            ->whereIn('users.team_id', $teamIds)
            ->select('users.team_id', 'tickets.status', DB::raw('count(*) as total'))
            ->groupBy('users.team_id', 'tickets.status')
            ->get()
            ->groupBy('team_id');

        $avgResolutionByTeam = Ticket::join('users', 'tickets.assigned_to', '=', 'users.id')
            ->whereIn('users.team_id', $teamIds)
            ->whereIn('tickets.status', ['resolved', 'closed'])
            ->whereNotNull('tickets.resolved_at')
            ->select('users.team_id', DB::raw('AVG(TIMESTAMPDIFF(HOUR, tickets.created_at, tickets.resolved_at)) as avg_hours'))
            ->groupBy('users.team_id')
            ->pluck('avg_hours', 'team_id');

        $teamStats = $teams->map(function ($team) use ($statusCounts, $avgResolutionByTeam) {
            $counts = ($statusCounts->get($team->id) ?? collect())->pluck('total', 'status');

            $open = $counts->get('open', 0);
            $assigned = $counts->get('assigned', 0);
            $inProgress = $counts->get('in_progress', 0);
            $waiting = $counts->get('waiting_for_customer', 0);
            $resolved = $counts->get('resolved', 0);
            $closed = $counts->get('closed', 0);
            $escalated = $counts->get('escalated', 0);
            $reopened = $counts->get('reopened', 0);

            $avgHours = $avgResolutionByTeam->get($team->id);

            return [
                'team_id' => $team->id,
                'team_name' => $team->team_name,
                'agent_count' => $team->agents_count,
                'total' => $open + $assigned + $inProgress + $waiting + $resolved + $closed + $escalated + $reopened,
                'open' => $open,
                'open_active' => $open + $assigned + $inProgress + $waiting + $reopened,
                'escalated' => $escalated,
                'completed' => $resolved + $closed,
                'avg_resolution' => $avgHours !== null ? round($avgHours, 1) : null,
            ];
        });

        $data = [
            'teamTickets' => $teamStats->sum('total'),
            'openTickets' => $teamStats->sum('open'),
            'escalatedTickets' => $teamStats->sum('escalated'),
            'overdueTickets' => $this->countOverdueTickets($teamIds),
            'teamStats' => $teamStats,
        ];

        return view('supervisor.dashboard', compact('data'));
    }

    private function countOverdueTickets($teamIds)
    {
        return Ticket::join('users', 'tickets.assigned_to', '=', 'users.id')
            ->whereIn('users.team_id', $teamIds)
            ->where('tickets.due_date', '<', now())
            ->whereNotIn('tickets.status', ['resolved', 'closed'])
            ->count();
    }
}
