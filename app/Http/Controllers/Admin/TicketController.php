<?php

namespace App\Http\Controllers\Admin;

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
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TicketController extends Controller
{

    /**
     * Tampilkan semua tiket dengan filter opsional.
     */
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

        return view('admin.ticket.index', compact('tickets', 'categories', 'priorities'));
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
    public function store(TicketRequest $request, TicketSlaService $slaService): RedirectResponse
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

        $allLabels      = Label::select('id', 'label_name')->orderBy('label_name')->get();
        $allowedStatuses = $ticketStatusService->allowedTransitions($ticket->status);
        $statusColorMap  = $ticketStatusService->statusColorMap();

        return view('admin.ticket.show', compact(
            'ticket',
            'agents',
            'allLabels',
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
            ->route('admin.tickets.show', $ticket)
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
            ->route('admin.tickets.index')
            ->with('success', "Tiket {$ticketNumber} berhasil dihapus.");
    }

    public function assign(Request $request, Ticket $ticket, TicketStatusService $ticketStatusService): RedirectResponse
    {
        $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id', function ($value) {
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

        $ticket->update(['assigned_to' => $newAgentId]);

        if ($newAgentId && $previousStatus === 'open') {
            $ticketStatusService->transition($ticket, 'assigned');
        }

        if (!$newAgentId && $previousStatus === 'assigned') {
            $ticket->update(['status' => 'open']);
        }

        $message = $newAgentId
            ? 'Agent berhasil di-assign ke tiket.'
            : 'Agent berhasil di-unassign dari tiket.';

        return redirect()->back()->with('success', $message);
    }

    public function status(Request $request, Ticket $ticket, TicketStatusService $ticketStatusService ): RedirectResponse
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
}
