<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SlaRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $slaRuleId = $this->route('sla_rule')?->id;

        return [
            'priority_id' => [
                'required',
                Rule::exists('priorities', 'id'),
                Rule::unique('sla_rules', 'priority_id')->ignore($slaRuleId),
            ],
            'response_time' => ['required', 'integer', 'min:1'],
            'resolution_time' => ['required', 'integer', 'min:1', 'gt:response_time'],
        ];
    }
}
