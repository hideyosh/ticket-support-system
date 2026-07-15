<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalTickets' => Ticket::count(),

            'overdueTickets' => Ticket::where('due_date', '<', now())
                ->whereNotIn('status', ['resolved', 'closed'])
                ->count(),

            'unassignedTickets' => Ticket::whereNull('assigned_to')->count(),

            'thisWeekTickets' => Ticket::whereBetween('created_at', [
                now()->startOfWeek(),
                now(),
            ])->count(),

            'ticketsByStatus' => Ticket::selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->get(),

            'ticketsByPriority' => Ticket::selectRaw('priority_id, count(*) as total')
                ->groupBy('priority_id')
                ->with('priority')
                ->get(),

            'ticketsByCategory' => Ticket::selectRaw('category_id, count(*) as total')
                ->groupBy('category_id')
                ->with('category')
                ->get(),

            'topAgents' => User::whereHas('role', fn ($q) => $q->where('role_name', 'agent'))
                ->withCount([
                    'assignedTickets as resolved_count' => fn ($q) => $q->where('status', 'resolved'),
                ])
                ->orderBy('resolved_count', 'desc')
                ->limit(5)
                ->get(),

            'avgResolutionTime' => Ticket::whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
                ->value('avg_hours'),
        ];

        return view('admin.dashboard', $data);
    }
}
