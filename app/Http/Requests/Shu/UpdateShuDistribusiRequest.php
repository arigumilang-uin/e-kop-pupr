<?php

namespace App\Http\Requests\Shu;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShuDistribusiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'persen' => 'required|numeric|min:0|max:100',
            'keterangan' => 'nullable|string',
        ];
    }
}
