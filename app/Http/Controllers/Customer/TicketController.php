<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Models\User;
use App\Models\Category;
use App\Models\Label;
use App\Models\Priority;
use App\Models\SlaRule;
use App\Models\Ticket;
use App\Notifications\TicketCreatedNotification;
use Illuminate\Support\Facades\Notification;
use App\Services\ActivityLogger;
use App\Services\TicketSlaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $query = Ticket::with(['category', 'priority', 'labels'])
            ->where('created_by', auth()->id());

        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('priority_id')) {
            $query->where('priority_id', request('priority_id'));
        }

        if (request()->filled('category_id')) {
            $query->where('category_id', request('category_id'));
        }

        $tickets = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::select('id', 'category_name')->orderBy('category_name')->get();
        $priorities = Priority::select('id', 'priority_name')->get();

        return view('customer.ticket.index', compact('tickets', 'categories', 'priorities'));
    }

    public function create(): View
    {
        $categories = Category::select('id', 'category_name')->orderBy('category_name')->get();
        $priorities = Priority::select('id', 'priority_name')->get();
        $labels = Label::select('id', 'label_name')->orderBy('label_name')->get();

        return view('customer.ticket.create', compact('categories', 'priorities', 'labels'));
    }

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

                    ActivityLogger::log($ticket, 'Attachment uploaded');
                }
            }

            return $ticket;
        });
        Notification::send(User::whereHas('role', fn ($q) => $q->whereIn('role_name', ['admin', 'supervisor']))->get(), new TicketCreatedNotification($ticket));

        return redirect()->route('customer.tickets.show', $ticket)->with('success', "Tiket berhasil dibuat.");
    }

    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $logs = $ticket->customerVisibleLogs()->get();

        $ticket->load([
            'creator',
            'assignedAgent',
            'category',
            'priority',
            'labels',
            'comments.user',
            'attachments',
        ]);

        return view('customer.ticket.show', compact('ticket', 'logs'));
    }
}
