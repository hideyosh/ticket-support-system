<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TicketRequest extends FormRequest
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
     * Rules ini bersifat universal untuk semua role (Admin, Supervisor, Customer)
     * dan mendukung aksi store (create) maupun update.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Judul tiket - required, string, maksimal 255 karakter
            'title' => ['required', 'string', 'max:255'],

            // Deskripsi tiket - required, string, tidak ada batas maksimal
            'description' => ['required', 'string'],

            // Kategori tiket - required, harus ada di tabel categories
            'category_id' => ['required', Rule::exists('categories', 'id')],

            // Prioritas tiket - required, harus ada di tabel priorities
            'priority_id' => ['required', Rule::exists('priorities', 'id')],

            // Label tiket - optional (nullable), array, setiap item harus ada di tabel labels
            // Note: ticket_number & created_by di-generate/diset otomatis di controller, tidak perlu di sini
            'labels' => ['nullable', 'array'],
            'labels.*' => ['exists:labels,id'],
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
            // Error messages untuk field 'title'
            'title.required' => 'Judul tiket wajib diisi.',
            'title.string' => 'Judul tiket harus berupa teks.',
            'title.max' => 'Judul tiket maksimal 255 karakter.',

            // Error messages untuk field 'description'
            'description.required' => 'Deskripsi tiket wajib diisi.',
            'description.string' => 'Deskripsi tiket harus berupa teks.',

            // Error messages untuk field 'category_id'
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',

            // Error messages untuk field 'priority_id'
            'priority_id.required' => 'Prioritas wajib dipilih.',
            'priority_id.exists' => 'Prioritas yang dipilih tidak valid.',

            // Error messages untuk field 'labels'
            'labels.array' => 'Format label tidak valid.',
            'labels.*.exists' => 'Salah satu label yang dipilih tidak valid.',
        ];
    }
}
