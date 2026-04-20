@extends('layouts.app')

@section('title', 'Tambah Anggota Baru')
@section('subtitle', 'Formulir pendaftaran anggota baru Koperasi PUPR Riau')

@section('actions')
<a href="{{ route('anggota.index') }}" class="py-2.5 px-4 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 text-sm font-medium transition-colors">
    Batal & Kembali
</a>
@endsection

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8">
        <form action="{{ route('anggota.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">NIP <span class="text-red-500">*</span></label>
                    <input type="text" name="nip" value="{{ old('nip') }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors text-sm">
                    @error('nip') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors text-sm">
                    @error('nama') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Golongan <span class="text-red-500">*</span></label>
                    <input type="text" name="golongan" value="{{ old('golongan') }}" required placeholder="Misal: III/a"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors text-sm">
                    @error('golongan') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Jabatan</label>
                    <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors text-sm">
                    @error('jabatan') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Bidang Dinas <span class="text-red-500">*</span></label>
                    <select name="bidang_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors text-sm">
                        <option value="">-- Pilih Bidang --</option>
                        @foreach($bidangs as $bidang)
                            <option value="{{ $bidang->id }}" {{ old('bidang_id') == $bidang->id ? 'selected' : '' }}>
                                {{ $bidang->nama_bidang }}
                            </option>
                        @endforeach
                    </select>
                    @error('bidang_id') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">No Handphone (WA)</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors text-sm">
                    @error('no_hp') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat Tempat Tinggal</label>
                    <textarea name="alamat" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors text-sm">{{ old('alamat') }}</textarea>
                    @error('alamat') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 mt-6 flex justify-end">
                <button type="submit" class="py-2.5 px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors shadow-sm">
                    Simpan Anggota Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
