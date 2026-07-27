<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Hanya Admin yang boleh mengupdate data user lain.
     */
    public function authorize(): bool
    {
        return $this->user()?->role?->role_name === 'admin';
    }

    public function rules(): array
    {
        // Ambil instance user yang sedang di-update dari route model binding
        $user = $this->route('user');

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => [
                'nullable',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
                'confirmed',
            ],
            'role_id'  => ['required', Rule::exists('roles', 'id')],
            'team_id'  => ['nullable', Rule::exists('teams', 'id')],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'  => strip_tags($this->name ?? ''),
            'email' => strtolower(trim($this->email ?? '')),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama wajib diisi.',
            'name.max'           => 'Nama maksimal 255 karakter.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah digunakan oleh user lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role_id.required'   => 'Role wajib dipilih.',
            'role_id.exists'     => 'Role yang dipilih tidak valid.',
            'team_id.exists'     => 'Tim yang dipilih tidak valid.',
        ];
    }
}
