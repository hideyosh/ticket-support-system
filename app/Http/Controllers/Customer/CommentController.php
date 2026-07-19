<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        abort_if($ticket->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,pdf,docx',
        ]);

        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
        ]);

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');

            $ticket->attachments()->create([
                'user_id' => auth()->id(),
                'file_name' => $request->file('attachment')->getClientOriginalName(),
                'file_path' => $path,
            ]);
        }

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }
}
