<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx'],
        ]);

        $comment = DB::transaction(function () use ($ticket, $validated, $request) {
            $comment = $ticket->comments()->create([
                'user_id' => auth()->id(),
                'body'    => $validated['body'],
                'type'    => 'public_comment',
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $storedPath = $file->store('attachments', 'public');

                    $comment->attachments()->create([
                        'uploaded_by'   => auth()->id(),
                        'original_name' => $file->getClientOriginalName(),
                        'stored_name'   => basename($storedPath),
                        'path'          => $storedPath,
                        'mime_type'     => $file->getClientMimeType(),
                        'size'          => $file->getSize(),
                    ]);
                }
            }

            return $comment;
        });


        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function destroy(Ticket $ticket, Comment $comment)
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
