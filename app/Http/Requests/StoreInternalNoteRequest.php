<?php

namespace App\Http\Requests;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class StoreInternalNoteRequest extends FormRequest
{
    /**
     * Hanya Admin, Supervisor, dan Agent yang boleh membuat internal note.
     * Memanfaatkan CommentPolicy::create() dengan tipe 'internal_note'.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', [Comment::class, 'internal_note']);
    }

    public function rules(): array
    {
        return [
            'body'           => ['required', 'string', 'min:3', 'max:5000'],
            'attachments'    => ['nullable', 'array', 'max:5'],
            'attachments.*'  => [
                'file',
                'max:2048',
                'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,zip',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Paksa type = internal_note agar tidak bisa dimanipulasi dari request
        $this->merge([
            'type' => 'internal_note',
            'body' => strip_tags($this->body ?? ''),
        ]);
    }

    public function messages(): array
    {
        return [
            'body.required'        => 'Isi catatan internal wajib diisi.',
            'body.min'             => 'Catatan minimal 3 karakter.',
            'body.max'             => 'Catatan maksimal 5000 karakter.',
            'attachments.max'      => 'Maksimal 5 file lampiran.',
            'attachments.*.file'   => 'Lampiran harus berupa file.',
            'attachments.*.max'    => 'Ukuran setiap lampiran maksimal 2 MB.',
            'attachments.*.mimes'  => 'Format file tidak didukung.',
        ];
    }
}
