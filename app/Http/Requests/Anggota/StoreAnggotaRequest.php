<?php

namespace App\Http\Requests\Anggota;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnggotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nip' => ['required', 'string', 'max:50', 'unique:anggota,nip'],
            'nama' => ['required', 'string', 'max:150'],
            'bidang_id' => ['required', 'exists:bidang,id'],
            'golongan_asn' => ['required', 'in:pns,pppk'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'tanggal_masuk' => ['nullable', 'date'],
            'tpp_mulai_pokok' => ['required', 'integer', 'min:1', 'max:12'],
            'tpp_tahun_pokok' => ['required', 'integer', 'min:2020', 'max:2050'],
            'tpp_mulai_wajib' => ['required', 'integer', 'min:1', 'max:12'],
            'tpp_tahun_wajib' => ['required', 'integer', 'min:2020', 'max:2050'],
        ];
    }

    public function messages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'nama.required' => 'Nama wajib diisi.',
            'bidang_id.required' => 'Bidang wajib dipilih.',
            'bidang_id.exists' => 'Bidang tidak valid.',
            'golongan_asn.required' => 'Golongan ASN wajib dipilih.',
            'golongan_asn.in' => 'Golongan ASN tidak valid.',
        ];
    }
}
