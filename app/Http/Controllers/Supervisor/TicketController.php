<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Label;
use App\Models\Priority;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketStatusService;
use App\Exceptions\InvalidStatusTransitionException;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Notifications\TicketAssignedNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
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
        $agents     = User::whereHas('role', fn($q) => $q->where('role_name', 'agent'))
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('supervisor.ticket.index', compact('tickets', 'categories', 'priorities', 'labels', 'agents'));
    }

    public function show(Ticket $ticket, TicketStatusService $ticketStatusService): View
    {
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

        $supervisorId = auth()->id();
        $agents = User::whereHas('role', fn($q) => $q->where('role_name', 'agent'))
            ->whereHas('team', fn($q) => $q->where('supervisor_id', $supervisorId))
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $allowedStatuses = $ticketStatusService->allowedTransitions($ticket->status);
        $statusColorMap  = $ticketStatusService->statusColorMap();

        return view('supervisor.ticket.show', compact(
            'ticket',
            'agents',
            'allowedStatuses',
            'statusColorMap'
        ));
    }

    public function assign(Request $request, Ticket $ticket, TicketStatusService $ticketStatusService): RedirectResponse
    {
        $supervisorId = auth()->id();

        $request->validate([
            'assigned_to' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($supervisorId) {
                    if ($value) {
                        $isSubordinate = User::where('id', $value)
                            ->whereHas('role', fn($q) => $q->where('role_name', 'agent'))
                            ->whereHas('team', fn($q) => $q->where('supervisor_id', $supervisorId))
                            ->exists();

                        if (!$isSubordinate) {
                            $fail('Agent yang dipilih bukan merupakan bawahan dalam tim Anda.');
                        }
                    }
                },
            ],
        ]);

        $previousStatus = $ticket->status;
        $newAgentId     = $request->assigned_to;
        $oldAgentId     = $ticket->assigned_to;

        $ticket->update(['assigned_to' => $newAgentId]);

        if ($newAgentId && $previousStatus === 'open') {
            $ticketStatusService->transition($ticket, 'assigned');
        }

        if (!$newAgentId && $previousStatus === 'assigned') {
            $ticket->update(['status' => 'open']);
        }

        if ($oldAgentId !== $newAgentId) {
            ActivityLogger::log(
                ticket: $ticket,
                action: 'Agent assign',
                field: 'assigned_to',
                old: $oldAgentId ? User::find($oldAgentId)?->name : null,
                new: $newAgentId ? User::find($newAgentId)?->name : null,
            );
        }

        $message = $newAgentId
            ? 'Agent berhasil di-assign ke tiket.'
            : 'Agent berhasil di-unassign dari tiket.';

        Notification::send(User::where('id', $newAgentId)->get(), new TicketAssignedNotification($ticket));

        return redirect()->back()->with('success', $message);
    }

    /**
     * Ubah status tiket dengan validasi transisi ketat via TicketStatusService.
     */
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
