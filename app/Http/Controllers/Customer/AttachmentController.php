<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttachmentRequest;
use App\Models\Attachment;
use App\Models\Ticket;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentController extends Controller
{
    /**
     * Store a newly created attachment in storage.
     */
    public function store(StoreAttachmentRequest $request, Ticket $ticket): \Illuminate\Http\JsonResponse
    {
        // Check if user owns the ticket
        if ($ticket->created_by !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $file = $request->file('attachment');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();

        // Generate unique stored filename using UUID
        $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Store file to public disk in attachments folder
        $path = $file->storeAs('attachments', $storedName, 'public');

        // Create attachment record
        $attachment = Attachment::create([
            'attachable_type' => Ticket::class,
            'attachable_id' => $ticket->id,
            'uploaded_by' => Auth::id(),
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'path' => $path,
            'mime_type' => $mimeType,
            'size' => $size,
        ]);

        return response()->json([
            'message' => 'File berhasil diunggah',
            'attachment' => $attachment,
        ], 201);
    }

    /**
     * Download an attachment.
     */
    public function download(Ticket $ticket, Attachment $attachment): BinaryFileResponse|Response
    {
        // Check if attachment belongs to the ticket
        if ($attachment->attachable_type !== Ticket::class || $attachment->attachable_id !== $ticket->id) {
            return response('Attachment not found', 404);
        }

        // Check if user owns the ticket
        if ($ticket->created_by !== Auth::id()) {
            return response('Unauthorized', 403);
        }

        // Check if file exists in storage
        if (!Storage::disk('public')->exists($attachment->path)) {
            return response('File not found', 404);
        }

        $filePath = Storage::disk('public')->path($attachment->path);

        return response()->download($filePath, $attachment->original_name);
    }
}
