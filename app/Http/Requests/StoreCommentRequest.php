<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization check dilakukan di Policy, di sini hanya true
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Validasi body komentar - required dan minimal 3 karakter
            'body' => ['required', 'string', 'min:3', 'max:5000'],

            // Validasi type komentar - hanya boleh public_comment atau internal_note
            'type' => ['required', 'string', 'in:public_comment,internal_note'],

            // Validasi attachments - optional, array, maksimal 5 file, setiap file maksimal 2MB
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:2048', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,zip'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'body.required' => 'Isi komentar wajib diisi.',
            'body.min' => 'Isi komentar minimal harus 3 karakter.',
            'body.max' => 'Isi komentar maksimal 5000 karakter.',
            'type.required' => 'Tipe komentar wajib diisi.',
            'type.in' => 'Tipe komentar hanya boleh: public_comment atau internal_note.',
            'attachments.array' => 'Attachments harus berupa array.',
            'attachments.max' => 'Attachments maksimal 5 file.',
            'attachments.*.file' => 'Setiap file harus berupa file yang valid.',
            'attachments.*.max' => 'Setiap file maksimal 2 MB.',
            'attachments.*.mimes' => 'File hanya boleh berupa: jpg, jpeg, png, pdf, doc, docx, xls, xlsx, txt, zip.',
        ];
    }

    /**
     * Prepare the data for validation.
     * Force type menjadi 'public_comment' jika user adalah Customer.
     * Ini mencegah Customer mengirim 'internal_note' via request body.
     */
    protected function prepareForValidation(): void
    {
        // Jika user login adalah Customer (role_name = 'customer'), paksa type menjadi public_comment
        if (auth()->check() && auth()->user()->role->role_name === 'customer') {
            // Set type menjadi public_comment secara otomatis untuk Customer
            // Ini memastikan Customer tidak bisa membuat internal_note meski dikirim via request body
            $this->merge([
                'type' => 'public_comment',
            ]);
        }
    }
}
