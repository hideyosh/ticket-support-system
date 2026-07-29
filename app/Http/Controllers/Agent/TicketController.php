<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Label;
use App\Models\Priority;
use App\Models\Ticket;
use App\Services\TicketStatusService;
use Illuminate\Http\RedirectResponse;
use App\Exceptions\InvalidStatusTransitionException;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $tickets = Ticket::with(['creator', 'assignedAgent', 'category', 'priority', 'labels'])
            ->filter($request->all(), auth()->user())
            ->paginate(15)
            ->withQueryString();

        $categories = Category::select('id', 'category_name')->orderBy('category_name')->get();
        $priorities = Priority::select('id', 'priority_name')->get();
        $labels     = Label::select('id', 'label_name')->orderBy('label_name')->get();

        return view('agent.ticket.index', compact('tickets', 'categories', 'priorities', 'labels'));
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
