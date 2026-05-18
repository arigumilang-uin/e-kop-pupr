<?php

namespace App\Http\Requests\PiutangEksternal;

use App\Enums\KategoriPeminjam;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePiutangEksternalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_peminjam' => ['required', 'string', 'max:150'],
            'jabatan_peminjam' => ['nullable', 'string', 'max:100'],
            'kategori_peminjam' => ['required', new Enum(KategoriPeminjam::class)],
            'tahun_pinjam' => [
                'required',
                'integer',
                'min:2015',
                function ($attribute, $value, $fail) {
                    // Piutang eksternal anggota adalah legacy (dari periode pengurus sebelumnya), tidak boleh tahun yang sama dengan saat ini.
                    if ($this->input('kategori_peminjam') === KategoriPeminjam::Anggota->value && (int) $value >= now()->year) {
                        $fail('Untuk Piutang Anggota, tahun pinjam harus periode sebelum tahun saat ini (' . now()->year . ').');
                    } elseif ((int) $value > now()->year) {
                        $fail('Tahun pinjam tidak boleh melebihi tahun berjalan.');
                    }
                },
            ],
            'nominal_awal' => ['required', 'numeric', 'min:1000'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'tanggal_catat' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_peminjam.required' => 'Nama peminjam wajib diisi.',
            'kategori_peminjam.required' => 'Kategori peminjam wajib dipilih.',
            'tahun_pinjam.required' => 'Tahun pinjam wajib diisi.',
            'tahun_pinjam.max' => 'Tahun pinjam tidak boleh melebihi tahun berjalan.',
            'nominal_awal.required' => 'Nominal piutang wajib diisi.',
            'nominal_awal.min' => 'Nominal piutang minimal Rp 1.000.',
        ];
    }
}
