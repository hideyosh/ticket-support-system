<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'priority_id' => ['required', Rule::exists('priorities', 'id')],
            // Labels bersifat opsional; ticket_number & created_by TIDAK ada di sini
            // karena keduanya di-generate/diset otomatis di controller
            'labels'      => ['nullable', 'array'],
            'labels.*'    => ['exists:labels,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Judul tiket wajib diisi.',
            'description.required' => 'Deskripsi tiket wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
            'priority_id.required' => 'Prioritas wajib dipilih.',
            'priority_id.exists'   => 'Prioritas yang dipilih tidak valid.',
            'labels.array'         => 'Format label tidak valid.',
            'labels.*.exists'      => 'Salah satu label yang dipilih tidak valid.',
        ];
    }
}
