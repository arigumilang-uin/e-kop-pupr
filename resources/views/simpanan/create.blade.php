@extends('layouts.app')

@section('title', 'Pencatatan Simpanan')
@section('subtitle', 'Formulir setoran/simpanan uang manual oleh anggota koperasi')

@section('actions')
<x-back-button fallback="{{ route('simpanan.index') }}">Batal & Kembali</x-back-button>
@endsection

@section('content')
<div class="max-w-3xl">
    
    {{-- Peringatan Info --}}
    <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 flex flex-col sm:flex-row gap-4 items-start sm:items-center">
        <div class="w-10 h-10 shrink-0 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <h3 class="text-sm font-semibold text-blue-800">Syarat Approval Pinjaman</h3>
            <p class="text-xs text-blue-600 mt-1">Uang yang dicatatkan di sini akan otomatis menambah <b>Kas Saldo Koperasi</b> yang memengaruhi kelayakan persetujuan pinjaman anggota di masa depan.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8">
        <form action="{{ route('simpanan.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Anggota Penyetor <span class="text-red-500">*</span></label>
                    <select name="anggota_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors text-sm">
                        <option value="">-- Cari dan Pilih Anggota Berdasarkan NIP/Nama --</option>
                        @foreach($anggotas as $anggota)
                            <option value="{{ $anggota->id }}" {{ old('anggota_id') == $anggota->id ? 'selected' : '' }}>
                                {{ $anggota->nip }} — {{ $anggota->nama }} ({{ $anggota->bidang ? $anggota->bidang->nama_bidang : '-' }})
                            </option>
                        @endforeach
                    </select>
                    @error('anggota_id') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Kategori Simpanan <span class="text-red-500">*</span></label>
                    <select name="jenis_simpanan_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors text-sm">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($jenisSimpanan as $jenis)
                            <option value="{{ $jenis->id }}" {{ old('jenis_simpanan_id') == $jenis->id ? 'selected' : '' }}>
                                {{ $jenis->nama }} 
                                @if($jenis->nominal_default > 0)
                                    (Standar: Rp {{ number_format($jenis->nominal_default, 0, ',', '.') }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_simpanan_id') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nominal Disetorkan (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="nominal" value="{{ old('nominal') }}" required min="1000"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors text-xl font-bold font-mono">
                    @error('nominal') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Setor <span class="text-red-500">*</span></label>
                    <x-datepicker name="tanggal" :value="old('tanggal', date('Y-m-d'))" :required="true" />
                    @error('tanggal') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi / Keterangan Tambahan</label>
                    <input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="Contoh: Titipan setoran via Transfer BNI..."
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors text-sm">
                    @error('keterangan') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2 p-5 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Opsional: Jika simpanan wajib per bulan</p>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] text-slate-600 mb-1">Bulan Untuk</label>
                            <select name="bulan_untuk" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                                <option value="">-- Kosong --</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ App\Helpers\FormatHelper::namaBulan($i) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-600 mb-1">Tahun Untuk</label>
                            <select name="tahun_untuk" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm bg-white">
                                <option value="">-- Kosong --</option>
                                @for($i = date('Y')-1; $i <= date('Y')+1; $i++)
                                    <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-200 mt-6 flex justify-end">
                <button type="submit" class="py-2.5 px-8 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 text-white text-sm font-medium transition-colors shadow-lg shadow-blue-500/25">
                    Proses Transaksi Simpanan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
