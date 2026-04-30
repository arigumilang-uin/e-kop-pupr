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
            'nip' => ['required', 'string', 'max:50', 'unique:anggota,nip,' . $anggotaId],
            'nama' => ['required', 'string', 'max:150'],
            'bidang_id' => ['required', 'exists:bidang,id'],
            'golongan_asn' => ['required', 'in:pns,pppk'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ];
    }
}
