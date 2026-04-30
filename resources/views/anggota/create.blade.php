@extends('layouts.app')

@section('title', 'Tambah Anggota Baru')
@section('subtitle', 'Formulir pendaftaran anggota baru Koperasi Tirta Bina Karya')

@section('actions')
<a href="{{ route('anggota.index') }}" class="py-2.5 px-4 rounded-xl bg-white border border-stone-200 text-stone-600 hover:bg-stone-50 hover:text-stone-900 text-sm font-medium transition-colors shadow-sm">
    <span class="hidden sm:inline">Batal & Kembali</span>
    <span class="sm:hidden">Kembali</span>
</a>
@endsection

@section('content')
<div class="max-w-4xl">
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden p-6 sm:p-8" x-data="{ nip: '{{ old('nip') }}' }">
        <form action="{{ route('anggota.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- NIP Component --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-bold text-stone-700 mb-1.5">NIP Anggota <span class="text-red-500">*</span></label>
                    <x-nip-input name="nip" model="nip" placeholder="Masukkan 18 digit angka NIP..." required="true" class="bg-stone-50 text-stone-800 font-mono tracking-wider focus:bg-white focus:ring-[#043d2e]/20 focus:border-[#043d2e]" />
                    @error('nip') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-stone-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Budi Santoso"
                           class="w-full h-12 px-4 rounded-xl border border-stone-200 bg-stone-50 focus:bg-white focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] transition-colors text-sm text-stone-800 outline-none">
                    @error('nama') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-stone-700 mb-1.5">Golongan ASN <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="golongan_asn" required class="w-full h-12 px-4 rounded-xl border border-stone-200 bg-stone-50 focus:bg-white focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] transition-colors text-sm text-stone-800 outline-none appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_16px_center] cursor-pointer">
                            <option value="">-- Pilih Golongan --</option>
                            @foreach(\App\Enums\GolonganAsn::cases() as $gol)
                                <option value="{{ $gol->value }}" {{ old('golongan_asn') === $gol->value ? 'selected' : '' }}>
                                    {{ $gol->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('golongan_asn') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-stone-700 mb-1.5">Bidang Dinas / Unit <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="bidang_id" required class="w-full h-12 px-4 rounded-xl border border-stone-200 bg-stone-50 focus:bg-white focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] transition-colors text-sm text-stone-800 outline-none appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_16px_center] cursor-pointer">
                            <option value="">-- Pilih Ruang Lingkup --</option>
                            @foreach($bidangs as $bidang)
                                <option value="{{ $bidang->id }}" {{ old('bidang_id') == $bidang->id ? 'selected' : '' }}>
                                    {{ $bidang->nama_bidang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('bidang_id') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-stone-700 mb-1.5">No Handphone (WA)</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 08123456789"
                           class="w-full h-12 px-4 rounded-xl border border-stone-200 bg-stone-50 focus:bg-white focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] transition-colors text-sm text-stone-800 outline-none">
                    @error('no_hp') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-stone-700 mb-1.5">Tanggal Masuk Koperasi</label>
                    <input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', now()->format('Y-m-d')) }}"
                           class="w-full h-12 px-4 rounded-xl border border-stone-200 bg-stone-50 focus:bg-white focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] transition-colors text-sm text-stone-800 outline-none cursor-pointer">
                    <p class="mt-1.5 text-[11px] font-medium text-stone-500 flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Potongan TPP berlaku di bulan berikutnya.</p>
                    @error('tanggal_masuk') <p class="mt-1.5 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-6 mt-8 border-t border-stone-100 flex justify-end">
                <button type="submit" class="py-2.5 px-6 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold transition-all shadow-md hover:shadow-lg active:scale-95 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Anggota Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
