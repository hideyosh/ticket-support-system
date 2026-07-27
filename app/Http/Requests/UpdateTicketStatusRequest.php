<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketStatusRequest extends FormRequest
{
    /**
     * Admin, Supervisor, dan Agent boleh mengubah status tiket.
     * Customer tidak diizinkan.
     */
    public function authorize(): bool
    {
        $role = $this->user()?->role?->role_name;
        return in_array($role, ['admin', 'supervisor', 'agent']);
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    'open',
                    'assigned',
                    'in_progress',
                    'waiting_for_customer',
                    'resolved',
                    'closed',
                    'reopened',
                    'escalated',
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status wajib dipilih.',
            'status.in'       => 'Status yang dipilih tidak valid.',
        ];
    }
}
