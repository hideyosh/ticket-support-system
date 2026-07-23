<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'type' => ['required', 'in:public_comment,internal_note'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,docx,xls,xlsx'],
        ]);

        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'body'    => $validated['body'],
            'type'    => $validated['type'],
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

    public function destroy(Ticket $ticket, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
