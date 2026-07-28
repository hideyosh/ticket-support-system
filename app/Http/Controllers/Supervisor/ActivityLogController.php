<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $ticketIds = auth()->user()->supervisedTickets()->pluck('tickets.id');
        $logs = ActivityLog::with(['user', 'ticket'])->whereIn('ticket_id', $ticketIds)
            ->when($request->filled('ticket_id'), fn($q) => $q->where('ticket_id', $request->ticket_id))
            ->when($request->filled('action'), fn($q) => $q->where('action', $request->action))
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('supervisor.activity-logs.index', compact('logs'));
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load(['user', 'ticket.priority', 'ticket.category']);

        return view('supervisor.activity-logs.show', compact('activityLog'));
    }
}
