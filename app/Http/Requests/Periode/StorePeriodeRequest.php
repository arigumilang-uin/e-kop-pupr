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
            'tanggal_buka' => ['required', 'date'],
            'tanggal_tutup' => ['required', 'date', 'after_or_equal:tanggal_buka'],
            'bulan_potongan_awal' => ['required', 'integer', 'min:1', 'max:12'],
            'bulan_potongan_akhir' => ['required', 'integer', 'min:1', 'max:12', 'gte:bulan_potongan_awal'],
            'limit_per_anggota' => ['required', 'numeric', 'min:100000'],
            'nominal_min' => ['required', 'numeric', 'min:100000', 'lte:limit_per_anggota'],
            'kelipatan_nominal' => ['required', 'numeric', 'min:50000'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_periode.required' => 'Nama periode wajib diisi.',
            'tanggal_buka.required' => 'Tanggal buka wajib diisi.',
            'tanggal_tutup.after_or_equal' => 'Tanggal tutup harus setelah atau sama dengan tanggal buka.',
            'bulan_potongan_awal.required' => 'Bulan potongan awal TPP wajib diisi.',
            'bulan_potongan_akhir.required' => 'Bulan potongan akhir TPP wajib diisi.',
            'bulan_potongan_akhir.gte' => 'Bulan potongan akhir harus sama atau setelah bulan potongan awal.',
            'limit_per_anggota.required' => 'Limit maks per anggota wajib diisi.',
            'limit_per_anggota.min' => 'Limit maks minimal Rp 100.000.',
            'nominal_min.required' => 'Nominal minimum wajib diisi.',
            'nominal_min.min' => 'Nominal minimum minimal Rp 100.000.',
            'nominal_min.lte' => 'Nominal minimum tidak boleh melebihi limit maksimum.',
            'kelipatan_nominal.required' => 'Kelipatan nominal wajib diisi.',
            'kelipatan_nominal.min' => 'Kelipatan nominal minimal Rp 50.000.',
        ];
    }
}
