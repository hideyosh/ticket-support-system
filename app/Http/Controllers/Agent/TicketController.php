<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Priority;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Tampilkan semua tiket yang ditugaskan ke Agent (auth()->id()).
     * Menggunakan Eager Loading untuk mencegah N+1 query.
     */
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

        return view('agent.tickets.index', compact('tickets', 'categories', 'priorities'));
    }

    /**
     * Tampilkan detail tiket.
     */
    public function show(Ticket $ticket): View
    {
        abort_if($ticket->assigned_to !== auth()->id(), 403);

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

        return view('agent.tickets.show', compact('ticket'));
    }
}
