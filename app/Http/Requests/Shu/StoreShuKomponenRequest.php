<?php

namespace App\Http\Requests\Shu;

use Illuminate\Foundation\Http\FormRequest;

class StoreShuKomponenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'tipe' => 'required|in:pendapatan,beban',
            'sumber_data' => 'required|string',
            'deskripsi' => 'nullable|string',
        ];
    }
}
