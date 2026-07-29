<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Requests\UpdateTicketStatusRequest;
use App\Models\Category;
use App\Models\Label;
use App\Models\Priority;
use App\Models\Ticket;
use App\Models\SlaRule;
use App\Models\User;
use App\Services\TicketSlaService;
use App\Services\TicketStatusService;
use App\Exceptions\InvalidStatusTransitionException;
use App\Notifications\TicketAssignedNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class TicketController extends Controller
{

    /**
     * Tampilkan semua tiket dengan filter opsional.
     */
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

        return view('admin.ticket.index', compact('tickets', 'categories', 'priorities', 'labels', 'agents'));
    }

    /**
     * Tampilkan form buat tiket baru.
     */
    public function create(): View
    {
        $categories = Category::select('id', 'category_name')->orderBy('category_name')->get();
        $priorities = Priority::select('id', 'priority_name')->get();
        $labels     = Label::select('id', 'label_name')->orderBy('label_name')->get();

        return view('admin.ticket.create', compact('categories', 'priorities', 'labels'));
    }

    /**
     * Simpan tiket baru ke database.
     * ticket_number di-generate otomatis; created_by = user yang sedang login.
     */
    public function store(StoreTicketRequest $request, TicketSlaService $slaService): RedirectResponse
    {
        $validated = $request->validated();
        $year = now()->year;
        $count = Ticket::whereYear('created_at', $year)->count() + 1;
        $validated['ticket_number'] = 'TCK-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->id();

        $slaRule = SlaRule::where('priority_id', $validated['priority_id'])->first();
        $validated['due_date'] = $slaRule
            ? $slaService->calculateDueDate(now(), $slaRule, 'open')
            : null;

        $ticket = DB::transaction(function () use ($validated, $request) {
            $ticket = Ticket::create($validated);
            $ticket->labels()->sync($request->input('labels', []));

            ActivityLogger::log($ticket, 'Ticket created');

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $storedPath = $file->store('attachments', 'public');

                    $ticket->attachments()->create([
                        'uploaded_by'   => auth()->id(),
                        'original_name' => $file->getClientOriginalName(),
                        'stored_name'   => basename($storedPath),
                        'path'          => $storedPath,
                        'mime_type'     => $file->getClientMimeType(),
                        'size'          => $file->getSize(),
                    ]);
                }
                ActivityLogger::log($ticket, 'Attachment uploaded');
            }

            return $ticket;
        });

        return redirect()->route('admin.tickets.show', $ticket)->with('success', "Tiket berhasil dibuat.");
    }
    /**
     * Tampilkan detail tiket.
     */
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

        $agents = User::whereHas('role', fn($q) => $q->where('role_name', 'agent'))
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $allowedStatuses = $ticketStatusService->allowedTransitions($ticket->status);
        $statusColorMap  = $ticketStatusService->statusColorMap();

        return view('admin.ticket.show', compact(
            'ticket',
            'agents',
            'allowedStatuses',
            'statusColorMap',
        ));
    }

    /**
     * Tampilkan form edit tiket.
     */
    public function edit(Ticket $ticket): View
    {
        $ticket->load('labels');

        $categories = Category::select('id', 'category_name')->orderBy('category_name')->get();
        $priorities = Priority::select('id', 'priority_name')->get();
        $labels     = Label::select('id', 'label_name')->orderBy('label_name')->get();

        return view('admin.ticket.edit', compact('ticket', 'categories', 'priorities', 'labels'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket, TicketSlaService $slaService): RedirectResponse
    {
        $validated = $request->validated();

        $priorityChanged = isset($validated['priority_id']) && $validated['priority_id'] !== $ticket->priority_id;
        $oldPriorityName = $ticket->priority?->priority_name;

        if ($priorityChanged) {
            $slaRule = SlaRule::where('priority_id', $validated['priority_id'])->first();

            $validated['due_date'] = $slaRule
                ? $slaService->calculateDueDate(now(), $slaRule, $ticket->status)
                : null;
        }

        $ticket->update($validated);
        $ticket->labels()->sync($request->labels ?? []);

        if ($priorityChanged) {
            ActivityLogger::log(
                ticket: $ticket,
                action: 'Priority changed',
                field: 'priority',
                old: $oldPriorityName,
                new: $ticket->fresh()->priority?->priority_name,
            );
        }

        return redirect()
            ->route('admin.tickets.show', $ticket)
            ->with('success', 'Tiket berhasil diperbarui.');
    }


    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticketNumber = $ticket->ticket_number;
        $ticket->delete();

        return redirect()
            ->route('admin.tickets.index')
            ->with('success', "Tiket berhasil dihapus.");
    }

    public function assign(Request $request, Ticket $ticket, TicketStatusService $ticketStatusService): RedirectResponse
    {
        $request->validate([
            'assigned_to' => [
                'nullable',
                'exists:users,id',
                function ($value, $fail) {
                    if (
                        $value && !User::where('id', $value)
                            ->whereHas('role', fn($q) => $q->where('role_name', 'agent'))
                            ->exists()
                    );
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

        $message = $newAgentId ? 'Agent berhasil di-assign ke tiket.' : 'Agent berhasil di-unassign dari tiket.';

        Notification::send(User::where('id', $newAgentId)->get(), new TicketAssignedNotification($ticket));

        return redirect()->back()->with('success', $message);
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
