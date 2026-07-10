<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LabelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $labelId = $this->route('label')?->id;

        return [
            'label_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('labels', 'label_name')->ignore($labelId),
            ],
        ];
    }
}
