<?php

namespace App\Http\Requests\Shu;

use App\Models\ShuDistribusi;
use Illuminate\Foundation\Http\FormRequest;

class StoreShuDistribusiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:100',
            'persen' => 'required|numeric|min:0.01|max:100',
            'deskripsi' => 'nullable|string',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $persenSudahAda = ShuDistribusi::aktif()->sum('persen');
            $persenBaru = (float) $this->persen;

            if (($persenSudahAda + $persenBaru) > 100) {
                $sisa = round(100 - $persenSudahAda, 2);
                $validator->errors()->add(
                    'persen',
                    "Total distribusi melebihi 100%. Saat ini sudah teralokasi {$persenSudahAda}%, sisa yang tersedia: {$sisa}%."
                );
            }
        });
    }
}
