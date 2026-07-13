<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teamId = $this->route('team') ? $this->route('team')->id : null;
        return [
            'team_name'    => ['required', 'string', 'max:255'],
            'supervisor_id'=> ['nullable', Rule::exists('users', 'id')],
        ];
    }
}
?>
