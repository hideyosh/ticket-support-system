<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;
use App\Models\Priority;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTickets      = Ticket::count();

        $overdueTickets    = Ticket::where('due_date', '<', now())
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();

        $unassignedTickets = Ticket::whereNull('assigned_to')
            ->count();

        $thisWeekTickets   = Ticket::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();

        $ticketsByStatus   = Ticket::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get();

        $ticketsByPriority = Priority::withCount('ticket')
            ->get();

        $ticketsByCategory = Category::withCount('ticket')
            ->get();

        $topAgents         = User::whereHas('role', fn($q) => $q->where('role_name', 'agent'))
            ->withCount([
                'assignedTickets as resolved_count' => fn($q) =>
                $q->where('status', 'resolved')
            ])
            ->orderBy('resolved_count', 'desc')
            ->limit(5)
            ->get();

        $avgResolutionTime = Ticket::whereNotNull('resolved_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');

        return view('admin.dashboard', compact(
            'totalTickets',
            'overdueTickets',
            'unassignedTickets',
            'thisWeekTickets',
            'ticketsByStatus',
            'ticketsByPriority',
            'ticketsByCategory',
            'topAgents',
            'avgResolutionTime',
        ));
    }
}
