<?php

namespace App\Http\Requests\Shu;

use App\Models\ShuDistribusi;
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
            'persen' => 'required|numeric|min:0.01|max:100',
            'deskripsi' => 'nullable|string',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $distribusi = $this->route('distribusi');

            // Total aktif MINUS distribusi yang sedang di-update + persen baru
            $persenLain = ShuDistribusi::aktif()
                ->where('id', '!=', $distribusi->id)
                ->sum('persen');

            $persenBaru = (float) $this->persen;

            if (($persenLain + $persenBaru) > 100) {
                $sisa = round(100 - $persenLain, 2);
                $validator->errors()->add(
                    'persen',
                    "Total distribusi melebihi 100%. Alokasi lainnya sudah {$persenLain}%, sisa yang tersedia: {$sisa}%."
                );
            }
        });
    }
}
