<?php

namespace App\Http\Requests\Pinjaman;

use Illuminate\Foundation\Http\FormRequest;

class RejectPinjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alasan_penolakan' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
        ];
    }
}
