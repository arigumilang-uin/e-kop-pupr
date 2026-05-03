<?php

namespace App\Http\Requests\Periode;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePeriodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $periode = $this->route('periode');
        $adaPengajuan = $periode->pinjaman()->exists();

        // Jika sudah ada pengajuan, hanya field non-kritikal yang boleh diubah
        if ($adaPengajuan) {
            return [
                'nama_periode' => ['required', 'string', 'max:100'],
                'tanggal_tutup' => ['required', 'date', 'after_or_equal:' . $periode->tanggal_buka->format('Y-m-d')],
                'catatan' => ['nullable', 'string', 'max:500'],
            ];
        }

        // Belum ada pengajuan → full edit
        return [
            'nama_periode' => ['required', 'string', 'max:100'],
            'tahun' => ['required', 'integer', 'min:2020', 'max:2050'],
            'tanggal_buka' => ['required', 'date'],
            'tanggal_tutup' => ['required', 'date', 'after_or_equal:tanggal_buka'],
            'batas_bulan_pelunasan' => ['required', 'integer', 'min:1', 'max:12'],
            'angsuran_bulan_berjalan' => ['nullable', 'boolean'],
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
            'tahun.required' => 'Tahun wajib diisi.',
            'tanggal_buka.required' => 'Tanggal buka wajib diisi.',
            'tanggal_tutup.after_or_equal' => 'Tanggal tutup harus setelah atau sama dengan tanggal buka.',
            'batas_bulan_pelunasan.required' => 'Batas bulan pelunasan wajib diisi.',
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
