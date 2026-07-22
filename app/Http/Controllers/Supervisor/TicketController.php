<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TicketRequest;
use App\Models\Category;
use App\Models\Label;
use App\Models\Priority;
use App\Models\Ticket;
use App\Models\SlaRule;
use App\Models\User;
use App\Services\TicketSlaService;
use App\Services\TicketStatusService;
use App\Exceptions\InvalidStatusTransitionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = Ticket::with(['creator', 'assignedAgent', 'category', 'priority', 'labels']);

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

        return view('supervisor.tickets.index', compact('tickets', 'categories', 'priorities'));
    }

    public function create(): View
    {
        $categories = Category::select('id', 'category_name')->orderBy('category_name')->get();
        $priorities = Priority::select('id', 'priority_name')->get();
        $labels     = Label::select('id', 'label_name')->orderBy('label_name')->get();

        return view('supervisor.tickets.create', compact('categories', 'priorities', 'labels'));
    }

    public function store(TicketRequest $request, TicketSlaService $slaService): RedirectResponse
    {
        $validated = $request->validated();
        $year = now()->year;
        $count = Ticket::whereYear('created_at', $year)->count() + 1;
        $validated['ticket_number'] = 'TCK-' . $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->id();

        $slaRule = SlaRule::where('priority_id', $validated['priority_id'])->first();

        if ($slaRule) {
            $validated['due_date'] = $slaService->calculateDueDate(now(), $slaRule, 'open');
        } else {
            $validated['due_date'] = null;
        }

        $ticket = Ticket::create($validated);
        $ticket->labels()->sync($request->labels ?? []);

        return redirect()
            ->route('supervisor.tickets.index')
            ->with('success', "Tiket {$ticket->ticket_number} berhasil dibuat.");
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

        $allLabels      = Label::select('id', 'label_name')->orderBy('label_name')->get();
        $allowedStatuses = $ticketStatusService->allowedTransitions($ticket->status);
        $statusColorMap  = $ticketStatusService->statusColorMap();

        return view('supervisor.tickets.show', compact(
            'ticket',
            'agents',
            'allLabels',
            'allowedStatuses',
            'statusColorMap'
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

        return view('supervisor.tickets.edit', compact('ticket', 'categories', 'priorities', 'labels'));
    }

    /**
     * Update data tiket (title, description, category, priority, due_date, labels).
     * ticket_number & created_by TIDAK boleh diubah dari sini.
     */
    public function update(TicketRequest $request, Ticket $ticket, TicketSlaService $slaService): RedirectResponse
    {
        $validated = $request->validated();

        $statusChanged = isset($validated['status']) && $validated['status'] !== $ticket->status;
        $priorityChanged = isset($validated['priority_id']) && $validated['priority_id'] !== $ticket->priority_id;

        if ($statusChanged || $priorityChanged) {
            $slaRule = SlaRule::where('priority_id', $validated['priority_id'])->first();

            if ($slaRule) {
                $status = $validated['status'] ?? $ticket->status;
                $validated['due_date'] = $slaService->calculateDueDate(now(), $slaRule, $status);
            } else {
                $validated['due_date'] = null;
            }
        }

        $ticket->update($validated);
        $ticket->labels()->sync($request->labels ?? []);

        return redirect()
            ->route('supervisor.tickets.show', $ticket)
            ->with('success', 'Tiket berhasil diperbarui.');
    }

    /**
     * Hapus tiket beserta relasinya (cascade di DB).
     */
    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticketNumber = $ticket->ticket_number;
        $ticket->delete();

        return redirect()
            ->route('supervisor.tickets.index')
            ->with('success', "Tiket {$ticketNumber} berhasil dihapus.");
    }

    // =========================================================================
    // Assign / Reassign
    // =========================================================================

    /**
     * Assign atau reassign agent ke tiket.
     * Supervisor HANYA boleh meng-assign agent yang merupakan bawahannya di suatu tim.
     */
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

        $ticket->update(['assigned_to' => $newAgentId]);

        // Auto-transition: open → assigned saat agent di-assign
        if ($newAgentId && $previousStatus === 'open') {
            $ticketStatusService->transition($ticket, 'assigned');
        }

        // Auto-revert: assigned → open saat agent di-unassign
        if (!$newAgentId && $previousStatus === 'assigned') {
            $ticket->update(['status' => 'open']);
        }

        $message = $newAgentId
            ? 'Agent berhasil di-assign ke tiket.'
            : 'Agent berhasil di-unassign dari tiket.';

        return redirect()->back()->with('success', $message);
    }

    // =========================================================================
    // Status Transition
    // =========================================================================

    /**
     * Ubah status tiket dengan validasi transisi ketat via TicketStatusService.
     */
    public function status(Request $request, Ticket $ticket, TicketStatusService $ticketStatusService): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string'],
        ]);

        try {
            $ticketStatusService->transition($ticket, $request->status);
            return redirect()->back()->with('success', 'Status tiket berhasil diperbarui.');
        } catch (InvalidStatusTransitionException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // =========================================================================
    // Labels
    // =========================================================================

    /**
     * Sync label pada tiket.
     */
    public function labels(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'labels'   => ['nullable', 'array'],
            'labels.*' => ['exists:labels,id'],
        ]);

        $ticket->labels()->sync($request->labels ?? []);

        return redirect()->back()->with('success', 'Label tiket berhasil diperbarui.');
    }

    // =========================================================================
    // Comments & Internal Notes
    // =========================================================================

    /**
     * Tambah komentar publik atau internal note ke tiket.
     */
    public function storeComment(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'type' => ['nullable', 'in:public_comment,internal_note'],
        ]);

        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
            'type'    => $request->type === 'internal_note' ? 'internal_note' : 'public_comment',
        ]);

        return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
