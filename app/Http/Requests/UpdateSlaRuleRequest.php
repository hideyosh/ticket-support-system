<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSlaRuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role?->role_name === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'priority_id' => [
                'required',
                Rule::exists('priorities', 'id'),
                Rule::unique('sla_rules', 'priority_id')->ignore($slaRuleId = $this->route('sla_rule')),
            ],
            'response_time'   => ['required', 'integer', 'min:1'],
            'resolution_time' => ['required', 'integer', 'min:1', 'gt:response_time'],
        ];
    }

    public function messages(): array
    {
        return [
            'priority_id.required'        => 'Prioritas wajib dipilih.',
            'priority_id.exists'          => 'Prioritas yang dipilih tidak valid.',
            'priority_id.unique'          => 'SLA Rule untuk prioritas ini sudah ada.',
            'response_time.required'      => 'Waktu respons wajib diisi.',
            'response_time.integer'       => 'Waktu respons harus berupa angka.',
            'response_time.min'           => 'Waktu respons minimal 1 jam.',
            'resolution_time.required'    => 'Waktu resolusi wajib diisi.',
            'resolution_time.integer'     => 'Waktu resolusi harus berupa angka.',
            'resolution_time.min'         => 'Waktu resolusi minimal 1 jam.',
            'resolution_time.gt'          => 'Waktu resolusi harus lebih besar dari waktu respons.',
        ];
    }
}
