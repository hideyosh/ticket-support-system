<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authorization check dilakukan di Policy (CommentPolicy), di sini hanya return true.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * Rules ini bersifat universal untuk semua action (store/update) dan mendukung
     * penambahan attachments (file lampiran) secara opsional.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Isi/body komentar - required, string, minimal 3 karakter, maksimal 5000 karakter
            'body' => ['required', 'string', 'min:3', 'max:5000'],

            // Tipe komentar - required, hanya boleh public_comment atau internal_note
            // Note: Customer akan dipaksa jadi 'public_comment' di prepareForValidation()
            'type' => ['required', 'string', 'in:public_comment,internal_note'],

            // Berkas lampiran - optional, array, maksimal 5 file
            // Setiap file harus valid dan maksimal 2MB
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'max:2048', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,zip'],
        ];
    }

    /**
     * Get the custom error messages for the defined validation rules.
     * Pesan error disediakan dalam Bahasa Indonesia untuk UX yang lebih baik.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Error messages untuk field 'body'
            'body.required' => 'Isi komentar wajib diisi.',
            'body.string' => 'Isi komentar harus berupa teks.',
            'body.min' => 'Isi komentar minimal harus 3 karakter.',
            'body.max' => 'Isi komentar maksimal 5000 karakter.',
            
            // Error messages untuk field 'type'
            'type.required' => 'Tipe komentar wajib diisi.',
            'type.string' => 'Tipe komentar harus berupa teks.',
            'type.in' => 'Tipe komentar hanya boleh: public_comment atau internal_note.',
            
            // Error messages untuk field 'attachments'
            'attachments.array' => 'Attachments harus berupa array.',
            'attachments.max' => 'Attachments maksimal 5 file.',
            'attachments.*.file' => 'Setiap file harus berupa file yang valid.',
            'attachments.*.max' => 'Setiap file maksimal 2 MB.',
            'attachments.*.mimes' => 'File hanya boleh berupa: jpg, jpeg, png, pdf, doc, docx, xls, xlsx, txt, zip.',
        ];
    }

    /**
     * Prepare the data for validation.
     * 
     * Method ini dipanggil SEBELUM validasi rules diterapkan.
     * Digunakan untuk memaksa tipe komentar menjadi 'public_comment' jika user adalah Customer.
     * 
     * Ini adalah bagian dari security strategy multi-layer:
     * 1. prepareForValidation() - memaksa tipe di request
     * 2. CommentPolicy - validasi di policy level
     * 3. scopeForUser() - filter di query level
     */
    protected function prepareForValidation(): void
    {
        // Periksa apakah user sudah login dan memiliki akses ke role
        if (auth()->check() && auth()->user()->role) {
            // Jika user adalah Customer, paksa type menjadi 'public_comment'
            // Ini memastikan Customer tidak bisa membuat internal_note
            // meskipun mereka mencoba mengirimkan 'internal_note' via request body
            if (auth()->user()->role->role_name === 'customer') {
                $this->merge([
                    'type' => 'public_comment',
                ]);
            }
        }
    }
}
