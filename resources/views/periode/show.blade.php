@extends('layouts.app')

@section('title', $periode->nama_periode)
@section('subtitle', 'Detail periode & link pengajuan pinjaman anggota')

@section('actions')
<x-back-button fallback="{{ route('periode.index') }}" />
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Kolom Kiri: Info & Link --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Link Share Card --}}
        <div class="bg-gradient-to-br from-stone-900 to-[#020202] border border-stone-800 rounded-2xl p-6 text-white shadow-xl shadow-stone-900/10">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-bold text-lg text-stone-100">Link Pengajuan Anggota</h3>
                    <p class="text-stone-400 text-sm mt-0.5">Bagikan link ini ke anggota untuk mengajukan pinjaman</p>
                </div>
                <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider
                    @if($periode->statusEfektif() === \App\Enums\StatusPeriode::Buka) bg-emerald-400/20 text-emerald-200 border border-emerald-400/30
                    @elseif($periode->statusEfektif() === \App\Enums\StatusPeriode::Terjadwal) bg-amber-400/20 text-amber-200 border border-amber-400/30
                    @else bg-red-400/20 text-red-200 border border-red-400/30 @endif">
                    {{ $periode->statusEfektif()->label() }}
                </span>
            </div>

            <div class="bg-black/30 rounded-xl p-4 flex items-center gap-3 mb-5 border border-white/10">
                <input type="text" id="link-pengajuan" value="{{ $periode->link_pengajuan }}" readonly
                       class="flex-1 bg-transparent text-emerald-50 text-sm font-mono border-none focus:outline-none truncate selection:bg-emerald-500/30">
                <button type="button" onclick="copyLink()" id="btn-copy"
                        class="shrink-0 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-white text-xs font-bold transition-colors uppercase tracking-wider shadow-sm">
                    Salin Link
                </button>
            </div>

            <div class="flex flex-wrap gap-2.5">
                {{-- Edit Periode --}}
                @can('periode.edit')
                <button type="button" x-data @click="$dispatch('open-modal', 'modal-edit-periode')"
                   class="px-4 py-2 rounded-xl bg-white/10 text-white text-xs font-bold hover:bg-white/20 transition-colors inline-flex items-center gap-1.5 border border-white/5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Periode
                </button>

                {{-- Tutup/Buka Periode --}}
                @if($periode->isBuka())
                <form method="POST" action="{{ route('periode.tutup', $periode) }}" class="inline"
                      onsubmit="return confirm('Yakin ingin menutup periode ini? Anggota tidak bisa mengajukan pinjaman lagi via link ini.')">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-500/20 text-red-200 border border-red-500/20 text-xs font-bold hover:bg-red-500/30 transition-colors inline-flex items-center gap-1.5">
                        Tutup Periode
                    </button>
                </form>
                @else
                <form method="POST" action="{{ route('periode.buka', $periode) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-200 border border-emerald-500/20 text-xs font-bold hover:bg-emerald-500/30 transition-colors inline-flex items-center gap-1.5">
                        Buka Kembali
                    </button>
                </form>
                @endif

                {{-- Reset Token --}}
                <form method="POST" action="{{ route('periode.reset-token', $periode) }}" class="inline"
                      onsubmit="return confirm('Yakin? Link lama tidak akan berfungsi lagi.')">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500/20 text-amber-200 border border-amber-500/20 text-xs font-bold hover:bg-amber-500/30 transition-colors inline-flex items-center gap-1.5">
                        Reset Link
                    </button>
                </form>
                @endcan

                {{-- Hapus Periode (hanya jika belum ada pengajuan) --}}
                @can('periode.delete')
                @if($periode->pinjaman->isEmpty())
                <form method="POST" action="{{ route('periode.destroy', $periode) }}" class="inline"
                      onsubmit="return confirm('Yakin ingin menghapus periode ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-red-600/30 text-red-200 border border-red-600/30 text-xs font-bold hover:bg-red-600/50 transition-colors inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Permanen
                    </button>
                </form>
                @endif
                @endcan
            </div>
        </div>

        {{-- Daftar Pengajuan di Periode Ini --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm flex flex-col">
            <div class="p-5 border-b border-stone-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-stone-800">Daftar Pengajuan Masuk</h3>
                    <p class="text-[13px] text-stone-500 font-medium mt-0.5">Seluruh pengajuan pada periode ini.</p>
                </div>
                <span class="inline-flex items-center justify-center bg-blue-50 text-blue-600 border border-blue-200/60 font-bold px-3 py-1 rounded-lg text-[13px] shadow-sm">
                    {{ $periode->pinjaman->count() }} Total
                </span>
            </div>

            @if($periode->pinjaman->isEmpty())
            <div class="p-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-stone-50 border border-stone-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h4 class="text-stone-800 font-bold mb-1">Belum Ada Pengajuan</h4>
                <p class="text-stone-500 text-sm">Anggota belum ada yang mengajukan pinjaman di periode ini.</p>
            </div>
            @else
            <div>
                <x-table>
                    <x-table.thead>
                        <x-table.th>Informasi Anggota</x-table.th>
                        <x-table.th class="text-right">Nominal Pengajuan</x-table.th>
                        <x-table.th class="text-right">Tenor</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th class="text-right">Tgl Form</x-table.th>
                    </x-table.thead>
                    <x-table.tbody>
                        @foreach($periode->pinjaman as $pinjaman)
                        <x-table.tr>
                            <x-table.td>
                                <div class="flex flex-col">
                                    <span class="font-bold text-stone-800">{{ $pinjaman->anggota->nama }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[11px] text-stone-500 font-mono font-medium tracking-wide">NIP. <x-nip-display :value="$pinjaman->anggota->nip" /></span>
                                    </div>
                                </div>
                            </x-table.td>
                            <x-table.td class="text-right">
                                <span class="font-mono text-[13px] font-bold text-stone-700">{{ format_rupiah($pinjaman->nominal_pinjaman) }}</span>
                            </x-table.td>
                            <x-table.td class="text-right">
                                <span class="text-stone-600 text-[13px] font-medium">{{ $pinjaman->tenor_bulan }} Bln</span>
                            </x-table.td>
                            <x-table.td>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider
                                    bg-{{ $pinjaman->status->color() }}-50 text-{{ $pinjaman->status->color() }}-700
                                    border border-{{ $pinjaman->status->color() }}-200">
                                    {{ $pinjaman->status->label() }}
                                </span>
                            </x-table.td>
                            <x-table.td class="text-right">
                                <span class="text-[12px] font-medium text-stone-500">{{ $pinjaman->tanggal_pengajuan->format('d/m/Y') }}</span>
                            </x-table.td>
                        </x-table.tr>
                        @endforeach
                    </x-table.tbody>
                </x-table>
            </div>
            @endif
        </div>
    </div>

    {{-- Kolom Kanan: Detail Info --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5"></div>
            <h3 class="font-bold text-stone-800 mb-5 text-[11px] uppercase tracking-widest flex items-center gap-2">
                <svg class="w-4 h-4 text-[#043d2e]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Informasi Periode
            </h3>
            <div class="space-y-4 relative z-10">
                <div>
                    <p class="text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-1">Tahun Anggaran</p>
                    <p class="text-sm font-bold text-stone-800">{{ $periode->tahun }}</p>
                </div>
                <div class="h-px bg-stone-100"></div>
                <div>
                    <p class="text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-1">Tanggal Buka</p>
                    <p class="text-sm font-bold text-stone-800">{{ $periode->tanggal_buka->format('d F Y') }}</p>
                </div>
                @if($periode->tanggal_tutup)
                <div class="h-px bg-stone-100"></div>
                <div>
                    <p class="text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-1">Tanggal Tutup</p>
                    <p class="text-sm font-bold text-stone-800">{{ $periode->tanggal_tutup->format('d F Y') }}</p>
                </div>
                @endif
                <div class="h-px bg-stone-100"></div>
                <div>
                    <p class="text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-1">Rentang Potongan TPP</p>
                    <p class="text-sm font-bold text-stone-800">{{ nama_bulan($periode->bulan_potongan_awal) }} — {{ nama_bulan($periode->bulan_potongan_akhir) }}</p>
                    <p class="text-[11px] font-medium text-stone-500 mt-1">Tenor tersedia: {{ $periode->tenorTersedia() }} bulan</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-1">Aksi Oleh</p>
                    <p class="text-sm font-bold text-stone-800">{{ $periode->pembuka->nama ?? '-' }}</p>
                </div>
                @if($periode->catatan)
                <div class="h-px bg-stone-100"></div>
                <div>
                    <p class="text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-1">Catatan</p>
                    <p class="text-sm font-medium text-stone-600 bg-stone-50 p-3 rounded-xl border border-stone-100 mt-2">{{ $periode->catatan }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@php
    $adaPengajuan = $periode->pinjaman->isNotEmpty();
@endphp

{{-- Modal Edit Periode --}}
<x-modal name="modal-edit-periode" title="Edit Periode Pinjaman" maxWidth="2xl">
    <form id="form-edit-periode" method="POST" action="{{ route('periode.update', $periode) }}" class="contents">
        @csrf
        @method('PUT')
        
        <x-slot name="subtitle">{{ $periode->nama_periode }}</x-slot>

        <div class="space-y-4">
            {{-- Banner peringatan jika ada pengajuan --}}
            @if($adaPengajuan)
            <div class="p-3 mb-2 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div>
                    <p class="text-[12px] font-bold text-amber-800 uppercase tracking-widest">PERHATIAN: Terdapat Pengajuan</p>
                    <p class="text-[11px] font-medium text-amber-600 mt-1 leading-relaxed">Hanya <strong>Nama Periode</strong>, <strong>Tanggal Tutup</strong>, dan <strong>Catatan Tambahan</strong> yang dapat diubah untuk menjaga integritas pinjaman.</p>
                </div>
            </div>
            @endif

            {{-- Nama Periode --}}
            <div class="space-y-1.5">
                <label for="nama_periode" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Nama Periode <span class="text-red-500">*</span></label>
                <input type="text" id="nama_periode" name="nama_periode" value="{{ old('nama_periode', $periode->nama_periode) }}" required
                       class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm"
                       placeholder="Contoh: Pinjaman Semester I 2026">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Bulan Potongan Awal --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Bulan Potongan Awal <span class="text-red-500">*</span></label>
                    <select name="bulan_potongan_awal" required
                            class="w-full px-4 py-3 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm
                                   {{ $adaPengajuan ? 'bg-stone-100 cursor-not-allowed text-stone-500 opacity-90' : 'bg-stone-50 cursor-pointer' }}"
                            {{ $adaPengajuan ? 'disabled' : '' }}>
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ old('bulan_potongan_awal', $periode->bulan_potongan_awal) == $i ? 'selected' : '' }}>{{ nama_bulan($i) }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Bulan Potongan Akhir --}}
                <div class="space-y-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Bulan Potongan Akhir <span class="text-red-500">*</span></label>
                    <select name="bulan_potongan_akhir" required
                            class="w-full px-4 py-3 border border-stone-200 rounded-xl text-stone-800 font-bold focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm
                                   {{ $adaPengajuan ? 'bg-stone-100 cursor-not-allowed text-stone-500 opacity-90' : 'bg-stone-50 cursor-pointer' }}"
                            {{ $adaPengajuan ? 'disabled' : '' }}>
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ old('bulan_potongan_akhir', $periode->bulan_potongan_akhir) == $i ? 'selected' : '' }}>{{ nama_bulan($i) }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Tanggal Buka --}}
                <div class="space-y-1.5">
                    <label for="tanggal_buka" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tanggal Buka <span class="text-red-500">*</span></label>
                    <x-datepicker name="tanggal_buka" :value="old('tanggal_buka', $periode->tanggal_buka->format('Y-m-d'))" :required="true" :disabled="$adaPengajuan" />
                </div>

                {{-- Tanggal Tutup --}}
                <div class="space-y-1.5">
                    <label for="tanggal_tutup" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Tanggal Tutup <span class="text-stone-400 normal-case capitalize text-[10px]">(Opsional)</span></label>
                    <x-datepicker name="tanggal_tutup" :value="old('tanggal_tutup', $periode->tanggal_tutup?->format('Y-m-d'))" placeholder="Pilih Tanggal (Bisa Dikosongkan)" />
                </div>
            </div>

            {{-- Konfigurasi Nominal Pinjaman --}}
            <div class="space-y-4 p-4 rounded-xl border {{ $adaPengajuan ? 'bg-stone-50/50 border-stone-200' : 'bg-stone-50 border-stone-200' }}">
                <h3 class="text-[11px] uppercase tracking-widest font-bold text-stone-600 flex items-center gap-2 mb-2">
                    Limit Pinjaman
                    @if($adaPengajuan)
                    <span class="text-[9px] font-bold text-stone-400 bg-stone-200/50 px-2 py-[1px] rounded uppercase border border-stone-200 ml-1">— TERKUNCI</span>
                    @endif
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label for="nominal_min" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Minimum <span class="text-red-500">*</span></label>
                        <x-currency-input name="nominal_min" :value="old('nominal_min', $periode->nominal_min)" :required="true" :disabled="$adaPengajuan" />
                    </div>

                    <div class="space-y-1.5">
                        <label for="limit_per_anggota" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Maksimum <span class="text-red-500">*</span></label>
                        <x-currency-input name="limit_per_anggota" :value="old('limit_per_anggota', $periode->limit_per_anggota)" :required="true" :disabled="$adaPengajuan" />
                    </div>

                    <div class="space-y-1.5">
                        <label for="kelipatan_nominal" class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Kelipatan <span class="text-red-500">*</span></label>
                        <x-currency-input name="kelipatan_nominal" :value="old('kelipatan_nominal', $periode->kelipatan_nominal)" :required="true" :disabled="$adaPengajuan" />
                    </div>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="space-y-1.5">
                <label for="catatan" class="text-[11px] font-bold text-stone-500 uppercase tracking-wider block">Catatan Tambahan <span class="text-stone-400 normal-case capitalize text-[10px]">(Opsional)</span></label>
                <textarea id="catatan" name="catatan" rows="2"
                          class="w-full px-4 py-3 bg-stone-50 border border-stone-200 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none transition-all shadow-sm resize-none"
                          placeholder="Catatan internal pengingat untuk periode ini...">{{ old('catatan', $periode->catatan) }}</textarea>
            </div>
        </div>

        <x-slot name="footer">
            <button type="button" @click="$dispatch('close-modal', 'modal-edit-periode')" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batalkan</button>
            <button type="submit" form="form-edit-periode" class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Perubahan
            </button>
        </x-slot>
    </form>
</x-modal>

<script>
function copyLink() {
    const input = document.getElementById('link-pengajuan');
    const btn = document.getElementById('btn-copy');
    navigator.clipboard.writeText(input.value).then(() => {
        btn.textContent = 'Tersalin!';
        btn.classList.remove('bg-emerald-500', 'hover:bg-emerald-400');
        btn.classList.add('bg-stone-800', 'text-emerald-400');
        setTimeout(() => {
            btn.textContent = 'Salin Link';
            btn.classList.add('bg-emerald-500', 'hover:bg-emerald-400');
            btn.classList.remove('bg-stone-800', 'text-emerald-400');
        }, 2000);
    });
}
</script>
@endsection
