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
use Illuminate\View\View;

class TicketController extends Controller
{
    // =========================================================================
    // CRUD Utama
    // =========================================================================

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

        return view('admin.tickets.index', compact('tickets', 'categories', 'priorities'));
    }

    /**
     * Tampilkan form buat tiket baru.
     */
    public function create(): View
    {
        $categories = Category::select('id', 'category_name')->orderBy('category_name')->get();
        $priorities = Priority::select('id', 'priority_name')->get();
        $labels     = Label::select('id', 'label_name')->orderBy('label_name')->get();

        return view('admin.tickets.create', compact('categories', 'priorities', 'labels'));
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

        if ($slaRule) {
            // karena ticket baru, status pasti 'open' (sesuai default migration)
            $validated['due_date'] = $slaService->calculateDueDate(now(), $slaRule, 'open');
        } else {
            $validated['due_date'] = null;
        }

        $ticket = Ticket::create($validated);
        $ticket->labels()->sync($request->labels ?? []);

        return redirect()
            ->route('admin.tickets.index')
            ->with('success', "Tiket {$ticket->ticket_number} berhasil dibuat.");
    }

    /**
     * Tampilkan detail tiket.
     */
    public function show(Ticket $ticket, TicketStatusService $ticketStatusService): View
    {
        $ticket->load(['creator', 'assignedAgent', 'category', 'priority', 'labels', 'comments.user']);

        $agents = User::whereHas('role', fn($q) => $q->where('role_name', 'agent'))
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $allLabels      = Label::select('id', 'label_name')->orderBy('label_name')->get();
        $allowedStatuses = $ticketStatusService->allowedTransitions($ticket->status);
        $statusColorMap  = $ticketStatusService->statusColorMap();

        return view('admin.tickets.show', compact(
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

        return view('admin.tickets.edit', compact('ticket', 'categories', 'priorities', 'labels'));
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
        // kalau status & priority TIDAK berubah, due_date TIDAK disentuh sama sekali
        // (tidak dimasukkan ke $validated, jadi $ticket->update() tidak akan menimpanya)

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

    // =========================================================================
    // Assign / Reassign
    // =========================================================================

    /**
     * Assign atau reassign agent ke tiket.
     * Hanya admin/supervisor yang boleh melakukan ini (dilindungi middleware role:admin).
     * Side-effect: jika tiket berstatus 'open' dan agent di-assign, status otomatis → 'assigned'.
     *              jika agent di-unassign dan status 'assigned', status otomatis → 'open'.
     */
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
     * Internal note hanya boleh dilihat oleh agent/supervisor/admin (ditangani di view).
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
