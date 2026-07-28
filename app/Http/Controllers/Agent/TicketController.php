<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Ticket;
use App\Services\TicketStatusService;
use Illuminate\Http\RedirectResponse;
use App\Exceptions\InvalidStatusTransitionException;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $agentId = auth()->id();

        $query = Ticket::with(['creator', 'assignedAgent', 'category', 'priority', 'labels'])
            ->where('assigned_to', $agentId);

        // Filter: pencarian no. tiket / judul
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        // Filter: status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: prioritas
        if ($request->filled('priority_id')) {
            $query->where('priority_id', $request->priority_id);
        }

        // Filter: kategori
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $tickets    = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::select('id', 'category_name')->orderBy('category_name')->get();
        $priorities = Priority::select('id', 'priority_name')->get();

        return view('agent.ticket.index', compact('tickets', 'categories', 'priorities'));
    }

    public function show(Ticket $ticket,  TicketStatusService $ticketStatusService): View
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'creator',
            'assignedAgent',
            'category',
            'priority',
            'labels',
            'comments.user',
            'attachments',
            'activityLogs' => fn($q) => $q->latest(),
        ]);

        $allowedStatuses = $ticketStatusService->allowedTransitions($ticket->status);
        $statusColorMap  = $ticketStatusService->statusColorMap();

        return view('agent.ticket.show', compact(
            'ticket',
            'allowedStatuses',
            'statusColorMap',
        ));
    }

    public function status(UpdateTicketStatusRequest $request, Ticket $ticket, TicketStatusService $ticketStatusService): RedirectResponse
    {
        $oldStatus = $ticket->status;

        try {
            $ticketStatusService->transition($ticket, $request->status);

            ActivityLogger::log(
                ticket: $ticket,
                action: 'Status changed',
                field: 'status',
                old: $oldStatus,
                new: $ticket->fresh()->status,
            );

            return redirect()->back()->with('success', 'Status tiket berhasil diperbarui.');
        } catch (InvalidStatusTransitionException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
