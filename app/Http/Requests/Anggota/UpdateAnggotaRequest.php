<?php

namespace App\Http\Requests\Anggota;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnggotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $anggotaId = $this->route('anggotum')->id ?? $this->route('anggota')->id ?? null;

        return [
            'nip' => ['required', 'string', 'max:20', 'unique:anggota,nip,' . $anggotaId],
            'nama' => ['required', 'string', 'max:100'],
            'golongan' => ['required', 'string', 'max:10'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'bidang_id' => ['required', 'exists:bidang,id'],
            'alamat' => ['nullable', 'string'],
            'no_hp' => ['nullable', 'string', 'max:15'],
        ];
    }
}
