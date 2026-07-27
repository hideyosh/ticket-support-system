<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Ticket;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreCommentRequest $request, Ticket $ticket)
    {
        $validated = $request->validated();

        $comment = DB::transaction(function () use ($ticket, $validated, $request) {
            $comment = $ticket->comments()->create([
                'user_id' => auth()->id(),
                'body'    => $validated['body'],
                'type'    => $validated['type'],
            ]);

            if ($validated['type'] === 'internal') {
                ActivityLogger::log($ticket, 'Internal comment added');
            } else {
                ActivityLogger::log($ticket, 'Public comment added');
            }

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
