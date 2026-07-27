<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketRequest extends FormRequest
{
    /**
     * Hanya user yang sudah login yang boleh membuat tiket.
     * Authorization role lebih lanjut diatur di middleware/route.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['required', 'string', 'max:10000'],
            'category_id'    => ['required', Rule::exists('categories', 'id')],
            'priority_id'    => ['required', Rule::exists('priorities', 'id')],
            'labels'         => ['nullable', 'array'],
            'labels.*'       => ['integer', 'exists:labels,id'],
            // Attachments: maks 5 file, tiap file maks 2 MB
            'attachments'    => ['nullable', 'array', 'max:2'],
            'attachments.*'  => [
                'file',
                'max:2048',
                'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
            ],
        ];
    }

    /**
     * Strip tags dari input teks untuk mencegah XSS.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'title'       => strip_tags($this->title ?? ''),
            'description' => strip_tags($this->description ?? ''),
        ]);
    }

    public function messages(): array
    {
        return [
            'title.required'          => 'Judul tiket wajib diisi.',
            'title.max'               => 'Judul tiket maksimal 255 karakter.',
            'description.required'    => 'Deskripsi tiket wajib diisi.',
            'category_id.required'    => 'Kategori wajib dipilih.',
            'category_id.exists'      => 'Kategori yang dipilih tidak valid.',
            'priority_id.required'    => 'Prioritas wajib dipilih.',
            'priority_id.exists'      => 'Prioritas yang dipilih tidak valid.',
            'labels.array'            => 'Format label tidak valid.',
            'labels.*.exists'         => 'Salah satu label tidak valid.',
            'attachments.max'         => 'Maksimal 5 file lampiran.',
            'attachments.*.file'      => 'Lampiran harus berupa file.',
            'attachments.*.max'       => 'Ukuran setiap lampiran maksimal 2 MB.',
            'attachments.*.mimes'     => 'Format file tidak didukung.',
        ];
    }
}
