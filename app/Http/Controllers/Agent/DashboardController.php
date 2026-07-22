<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function index()
    {
        $agentId = auth()->id();

        // 1. My assigned tickets
        $myAssignedTickets = Ticket::where('assigned_to', $agentId)->count();

        // 2. My overdue tickets
        $myOverdueTickets = Ticket::where('assigned_to', $agentId)
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();

        // 3. Tickets by status (grouped for agent)
        $ticketsByStatus = Ticket::where('assigned_to', $agentId)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get();

        // 4. Recently updated tickets (with eager loading to prevent N+1 queries)
        $recentlyUpdatedTickets = Ticket::where('assigned_to', $agentId)
            ->with([
                'creator:id,name,email',
                'assignedAgent:id,name,email',
                'category:id,category_name',
                'priority:id,priority_name',
                'labels:id,label_name',
            ])
            ->orderByDesc('updated_at')
            ->take(10)
            ->get();

        $data = [
            'myAssignedTickets'      => $myAssignedTickets,
            'myOverdueTickets'       => $myOverdueTickets,
            'ticketsByStatus'        => $ticketsByStatus,
            'recentlyUpdatedTickets' => $recentlyUpdatedTickets,
        ];

        return view('agent.dashboard', compact('data'));
    }
}
