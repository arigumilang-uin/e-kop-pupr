<?php

namespace App\Http\Requests\Periode;

use Illuminate\Foundation\Http\FormRequest;

class StorePeriodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_periode' => ['required', 'string', 'max:100'],
            'tahun' => ['required', 'integer', 'min:2020', 'max:2050'],
            'tanggal_buka' => ['required', 'date'],
            'tanggal_tutup' => ['nullable', 'date', 'after_or_equal:tanggal_buka'],
            'batas_bulan_pelunasan' => ['required', 'integer', 'min:1', 'max:12'],
            'limit_per_anggota' => ['required', 'numeric', 'min:100000'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_periode.required' => 'Nama periode wajib diisi.',
            'tahun.required' => 'Tahun wajib diisi.',
            'tanggal_buka.required' => 'Tanggal buka wajib diisi.',
            'tanggal_tutup.after_or_equal' => 'Tanggal tutup harus setelah atau sama dengan tanggal buka.',
            'batas_bulan_pelunasan.required' => 'Batas bulan pelunasan wajib diisi.',
            'limit_per_anggota.required' => 'Limit per anggota wajib diisi.',
            'limit_per_anggota.min' => 'Limit minimal Rp 100.000.',
        ];
    }
}
