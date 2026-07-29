<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidStatusTransitionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketEscalatedNotification;
use App\Notifications\TicketResolvedNotification;
use App\Services\ActivityLogger;
use App\Services\TicketSlaService;
use App\Services\TicketStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class TicketController extends Controller
{
    /**
     * List ticket, otomatis di-scope sesuai role user yang login lewat token.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $roleName = $user->role?->role_name;

        $tickets = Ticket::with(['category', 'priority', 'labels', 'creator', 'assignedAgent'])
            ->when($roleName === 'agent', fn($q) => $q->where('assigned_to', $user->id))
            ->when($roleName === 'customer', fn($q) => $q->where('created_by', $user->id))
            ->when($roleName === 'supervisor', fn($q) => $q->whereHas(
                'assignedAgent',
                fn($q2) => $q2->where('team_id', $user->team_id)
            ))
            ->latest()
            ->paginate(15);

        return TicketResource::collection($tickets);
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['category', 'priority', 'labels', 'creator', 'assignedAgent', 'comments.user', 'attachments']);

        return new TicketResource($ticket);
    }

    public function store(StoreTicketRequest $request, TicketSlaService $slaService)
    {
        $this->authorize('create', Ticket::class);

        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;

        $year = now()->year;
        $count = Ticket::whereYear('created_at', $year)->count() + 1;
        $validated['ticket_number'] = 'TCK-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);

        $ticket = Ticket::create($validated);
        $ticket->labels()->sync($request->input('labels', []));

        return (new TicketResource($ticket->load(['category', 'priority', 'labels', 'creator'])))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return response()->json(['message' => 'Ticket berhasil dihapus.']);
    }

    /**
     * Assign / unassign agent ke ticket.
     */
    public function assign(Request $request, Ticket $ticket, TicketStatusService $ticketStatusService)
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate([
            'assigned_to' => [
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if (
                        $value && !User::where('id', $value)
                            ->whereHas('role', fn($q) => $q->where('role_name', 'agent'))
                            ->exists()
                    ) {
                        $fail('User yang dipilih bukan seorang agent.');
                    }
                },
            ],
        ]);

        $previousStatus = $ticket->status;
        $newAgentId     = $validated['assigned_to'] ?? null;
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
                actor: $request->user(),
            );

            if ($newAgentId) {
                User::find($newAgentId)?->notify(new TicketAssignedNotification($ticket));
            }
        }

        return new TicketResource($ticket->fresh(['assignedAgent']));
    }

    /**
     * Ubah status ticket (transisi divalidasi oleh TicketStatusService).
     */
    public function status(Request $request, Ticket $ticket, TicketStatusService $ticketStatusService)
    {
        $this->authorize('update', $ticket);

        $validated = $request->validate([
            'status' => ['required', 'string'],
        ]);

        $oldStatus = $ticket->status;

        try {
            $ticketStatusService->transition($ticket, $validated['status']);
        } catch (InvalidStatusTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        ActivityLogger::log(
            ticket: $ticket,
            action: 'Status changed',
            field: 'status',
            old: $oldStatus,
            new: $ticket->fresh()->status,
            actor: $request->user(),
        );

        match ($validated['status']) {
            'resolved'  => $ticket->creator?->notify(new TicketResolvedNotification($ticket)),
            'escalated' => Notification::send($this->supervisorsAndAdmins(), new TicketEscalatedNotification($ticket)),
            default     => null,
        };

        return new TicketResource($ticket->fresh());
    }

    private function supervisorsAndAdmins()
    {
        return User::whereHas('role', fn($q) => $q->whereIn('role_name', ['admin', 'supervisor']))->get();
    }
}
