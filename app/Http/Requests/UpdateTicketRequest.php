<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    /**
     * Hanya admin yang boleh mengupdate tiket secara penuh.
     * Supervisor dan Agent hanya boleh update status via UpdateTicketStatusRequest.
     */
    public function authorize(): bool
    {
        $role = $this->user()?->role?->role_name;
        return in_array($role, ['admin', 'supervisor']);
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:10000'],
            'category_id' => ['required', Rule::exists('categories', 'id')],
            'priority_id' => ['required', Rule::exists('priorities', 'id')],
            'status'      => [
                'nullable',
                'string',
                Rule::in(['open','assigned','in_progress','waiting_for_customer','resolved','closed','reopened','escalated']),
            ],
            'labels'      => ['nullable', 'array'],
            'labels.*'    => ['integer', 'exists:labels,id'],
        ];
    }

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
            'title.required'       => 'Judul tiket wajib diisi.',
            'title.max'            => 'Judul tiket maksimal 255 karakter.',
            'description.required' => 'Deskripsi tiket wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
            'priority_id.required' => 'Prioritas wajib dipilih.',
            'priority_id.exists'   => 'Prioritas yang dipilih tidak valid.',
            'status.in'            => 'Status tiket tidak valid.',
            'labels.*.exists'      => 'Salah satu label tidak valid.',
        ];
    }
}
