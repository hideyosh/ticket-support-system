<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TicketRequest;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Label;
use App\Models\User;
use App\Services\TicketStatusService;
use App\Exceptions\InvalidStatusTransitionException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['creator', 'assignedAgent', 'category', 'priority', 'labels'])->latest()->paginate(15);
        return view('admin.tickets.index', compact('tickets'));
    }

    public function create()
    {
        $categories = Category::select('id', 'name')->get();
        $priorities = Priority::select('id', 'priority_name')->get();
        return view('admin.tickets.create', compact('categories', 'priorities'));
    }

    public function store(TicketRequest $request)
    {
        $validated = $request->validated();
        $validated['ticket_number'] = 'TKT-' . strtoupper(Str::random(8));
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'open';

        Ticket::create($validated);
        
        return redirect()->route('admin.tickets.index')->with('success', 'Tiket berhasil dibuat.');
    }

    public function show(Request $request, Ticket $ticket, TicketStatusService $ticketStatusService)
    {
        // Handle POST tambah komentar
        if ($request->isMethod('POST') && $request->has('_add_comment')) {
            $request->validate([
                'body' => 'required|string',
            ]);

            $ticket->comments()->create([
                'user_id' => auth()->id(),
                'body'    => $request->body,
                'type'    => $request->type === 'internal_note' ? 'internal_note' : 'public_comment',
            ]);

            return redirect()->back()->with('success', 'Komentar berhasil ditambahkan.');
        }

        $ticket->load(['creator', 'assignedAgent', 'category', 'priority', 'labels', 'comments.user']);

        $agents = User::whereHas('role', function ($q) {
            $q->where('name', 'agent');
        })->select('id', 'name')->get();

        $allLabels = Label::select('id', 'label_name')->get();

        $allowedStatuses = $ticketStatusService->allowedTransitions($ticket->status);

        return view('admin.tickets.show', compact('ticket', 'agents', 'allLabels', 'allowedStatuses'));
    }

    public function edit(Ticket $ticket)
    {
        $categories = Category::select('id', 'name')->get();
        $priorities = Priority::select('id', 'priority_name')->get();
        return view('admin.tickets.edit', compact('ticket', 'categories', 'priorities'));
    }

    public function update(TicketRequest $request, Ticket $ticket)
    {
        $ticket->update($request->validated());
        return redirect()->route('admin.tickets.index')->with('success', 'Tiket berhasil diperbarui.');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('admin.tickets.index')->with('success', 'Tiket berhasil dihapus.');
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate([
            'assigned_to' => [
                'nullable', 
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $isAgent = User::where('id', $value)->whereHas('role', function($q) {
                            $q->where('name', 'agent');
                        })->exists();
                        if (!$isAgent) {
                            $fail('User yang dipilih bukan agent.');
                        }
                    }
                }
            ],
        ]);

        $ticket->update([
            'assigned_to' => $request->assigned_to,
        ]);

        // Secara opsional, jika status open, otomatis jadi assigned? (Tidak diminta secara eksplisit)

        return redirect()->back()->with('success', 'Agent berhasil di-assign ke tiket.');
    }

    public function status(Request $request, Ticket $ticket, TicketStatusService $ticketStatusService)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        try {
            $ticketStatusService->transition($ticket, $request->status);
            return redirect()->back()->with('success', 'Status tiket berhasil diperbarui.');
        } catch (InvalidStatusTransitionException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function labels(Request $request, Ticket $ticket)
    {
        $request->validate([
            'labels' => 'array',
            'labels.*' => 'exists:labels,id',
        ]);

        $ticket->labels()->sync($request->labels ?? []);
        
        return redirect()->back()->with('success', 'Label tiket berhasil diperbarui.');
    }
}
