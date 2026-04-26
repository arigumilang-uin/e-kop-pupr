<?php

namespace App\Http\Requests\Sistem;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengaturanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'value' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'value.required' => 'Nilai pengaturan wajib diisi.',
        ];
    }
}
