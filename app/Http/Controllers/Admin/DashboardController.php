<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Tickets
        $totalTickets = Ticket::count();

        // 2. Tickets by Status
        $ticketsByStatus = Ticket::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 3. Tickets by Priority
        $ticketsByPriority = Ticket::selectRaw('priorities.priority_name, count(*) as count')
            ->join('priorities', 'tickets.priority_id', '=', 'priorities.id')
            ->groupBy('priorities.id', 'priorities.priority_name')
            ->orderByDesc('count')
            ->pluck('count', 'priority_name')
            ->toArray();

        // 4. Tickets by Category
        $ticketsByCategory = Ticket::selectRaw('categories.name, count(*) as count')
            ->join('categories', 'tickets.category_id', '=', 'categories.id')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('count')
            ->pluck('count', 'name')
            ->toArray();

        // 5. Overdue Tickets (belum selesai & due_date sudah lewat)
        $overdueTickets = Ticket::whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();

        // 6. Unassigned Tickets (assigned_to masih NULL)
        $unassignedTickets = Ticket::whereNull('assigned_to')->count();

        // 7. Tickets Created This Week
        $ticketsThisWeek = Ticket::whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek(),
        ])->count();

        // 8. Average Resolution Time (dalam jam), hanya tiket yang sudah resolved
        $avgResolutionTime = Ticket::whereNotNull('resolved_at')
            ->where('status', 'resolved')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->value('avg_hours');
        $avgResolutionTime = $avgResolutionTime ? round($avgResolutionTime, 1) : null;

        // 9. Top 5 Agents by Resolved Tickets
        $topAgents = User::withCount([
                'assignedTickets as resolved_count' => function ($query) {
                    $query->where('status', 'resolved');
                },
            ])
            ->whereHas('role', fn($q) => $q->where('name', 'agent'))
            ->having('resolved_count', '>', 0)
            ->orderByDesc('resolved_count')
            ->limit(5)
            ->get(['id', 'name']);

        return view('admin.dashboard.index', compact(
            'totalTickets',
            'ticketsByStatus',
            'ticketsByPriority',
            'ticketsByCategory',
            'overdueTickets',
            'unassignedTickets',
            'ticketsThisWeek',
            'avgResolutionTime',
            'topAgents',
        ));
    }
}
