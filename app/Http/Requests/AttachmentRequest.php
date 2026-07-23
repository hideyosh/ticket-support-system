<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authorization dilakukan di Policy, di sini hanya return true.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * Rules ini bersifat fleksibel dan dapat digunakan untuk:
     * - Upload single file (menggunakan 'attachment' atau 'file')
     * - Upload multiple files (menggunakan 'attachments' atau 'files')
     * 
     * Kontroller dapat menggunakan salah satu berdasarkan kebutuhan.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Rule untuk single file upload
            // Field 'attachment' atau 'file' - required, harus file, maksimal 2MB
            'attachment' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,zip,gif,webp,mp4,mov'],
            'file' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,zip,gif,webp,mp4,mov'],
            
            // Rule untuk multiple files upload
            // Field 'attachments' atau 'files' - optional, array, maksimal 10 file
            // Setiap file harus valid dan maksimal 2MB
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:2048', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,zip,gif,webp,mp4,mov'],
            'files' => ['nullable', 'array', 'max:10'],
            'files.*' => ['file', 'max:2048', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt,zip,gif,webp,mp4,mov'],
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
            // Error messages untuk single file upload
            'attachment.file' => 'File lampiran harus berupa file yang valid.',
            'attachment.max' => 'File lampiran tidak boleh lebih dari 2 MB.',
            'attachment.mimes' => 'File lampiran hanya boleh berupa: jpg, jpeg, png, pdf, doc, docx, xls, xlsx, txt, zip, gif, webp, mp4, mov.',
            'file.file' => 'File harus berupa file yang valid.',
            'file.max' => 'File tidak boleh lebih dari 2 MB.',
            'file.mimes' => 'File hanya boleh berupa: jpg, jpeg, png, pdf, doc, docx, xls, xlsx, txt, zip, gif, webp, mp4, mov.',
            
            // Error messages untuk multiple files upload
            'attachments.array' => 'Attachments harus berupa array.',
            'attachments.max' => 'Attachments maksimal 10 file.',
            'attachments.*.file' => 'Setiap file lampiran harus berupa file yang valid.',
            'attachments.*.max' => 'Setiap file lampiran tidak boleh lebih dari 2 MB.',
            'attachments.*.mimes' => 'Setiap file lampiran hanya boleh berupa: jpg, jpeg, png, pdf, doc, docx, xls, xlsx, txt, zip, gif, webp, mp4, mov.',
            'files.array' => 'Files harus berupa array.',
            'files.max' => 'Files maksimal 10 file.',
            'files.*.file' => 'Setiap file harus berupa file yang valid.',
            'files.*.max' => 'Setiap file tidak boleh lebih dari 2 MB.',
            'files.*.mimes' => 'Setiap file hanya boleh berupa: jpg, jpeg, png, pdf, doc, docx, xls, xlsx, txt, zip, gif, webp, mp4, mov.',
        ];
    }

    /**
     * Prepare the data for validation.
     * 
     * Normalize field names untuk flexibility.
     * Controller dapat menggunakan 'attachment' atau 'file' untuk single upload,
     * dan 'attachments' atau 'files' untuk multiple uploads.
     */
    protected function prepareForValidation(): void
    {
        // Jika ada field 'file', duplikasi ke 'attachment' untuk consistency
        if ($this->has('file') && !$this->has('attachment')) {
            $this->merge([
                'attachment' => $this->file('file'),
            ]);
        }

        // Jika ada field 'files', duplikasi ke 'attachments' untuk consistency
        if ($this->has('files') && !$this->has('attachments')) {
            $this->merge([
                'attachments' => $this->file('files'),
            ]);
        }
    }
}
