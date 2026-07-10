<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PriorityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $priorityId = $this->route('priority')?->id;

        return [
            'priority_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('priorities', 'priority_name')->ignore($priorityId),
            ],
        ];
    }
}
