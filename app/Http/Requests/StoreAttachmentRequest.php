<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttachmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'attachment' => ['required', 'file', 'max:2048', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'attachment.required' => 'File lampiran harus dipilih.',
            'attachment.file' => 'File lampiran harus berupa file yang valid.',
            'attachment.max' => 'File lampiran tidak boleh lebih dari 2 MB.',
            'attachment.mimes' => 'File lampiran hanya boleh berupa: jpg, jpeg, png, pdf, doc, docx, xls, xlsx.',
        ];
    }
}
