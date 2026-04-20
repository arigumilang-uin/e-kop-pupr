@extends('layouts.guest')

@section('title', 'Status Pengajuan Pinjaman')

@section('content')
<div class="w-full max-w-3xl py-6">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-white">Hasil Pengecekan Status</h1>
        <p class="text-slate-400 text-sm mt-1">Rincian pengajuan pinjaman terakhir Anda.</p>
    </div>

    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
        {{-- Header Info --}}
        <div class="p-6 border-b border-white/10 flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-sm text-slate-400 mb-1">Nomor Referensi</p>
                <p class="text-xl font-mono font-bold text-blue-400">{{ $pinjaman->no_referensi }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-slate-400 mb-1">Status Saat Ini</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                    @if($pinjaman->status->value === 'menunggu') bg-amber-500/10 text-amber-400 border border-amber-500/20
                    @elseif($pinjaman->status->value === 'disetujui') bg-blue-500/10 text-blue-400 border border-blue-500/20
                    @elseif($pinjaman->status->value === 'ditolak') bg-red-500/10 text-red-400 border border-red-500/20
                    @elseif($pinjaman->status->value === 'berjalan') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                    @else bg-slate-500/10 text-slate-400 border border-slate-500/20 @endif
                ">
                    {{ $pinjaman->status->label() }}
                </span>
            </div>
        </div>

        {{-- Detail Grid --}}
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Data Anggota --}}
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4 border-b border-white/10 pb-2">Informasi Anggota</h3>
                <div>
                    <p class="text-xs text-slate-500 mb-1">NIP</p>
                    <p class="text-sm font-medium text-slate-200">{{ $anggota->nip }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Nama Lengkap</p>
                    <p class="text-sm font-medium text-slate-200">{{ $anggota->nama }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-1">Tanggal Pengajuan</p>
                    <p class="text-sm font-medium text-slate-200">{{ $pinjaman->tanggal_pengajuan->format('d/m/Y H:i') }} WIB</p>
                </div>
                 <div>
                    <p class="text-xs text-slate-500 mb-1">Rekening Tujuan</p>
                    <p class="text-sm font-medium text-slate-200">{{ $pinjaman->nama_bank }} - {{ $pinjaman->no_rekening }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">a.n {{ $pinjaman->nama_rekening }}</p>
                </div>
            </div>

            {{-- Data Pinjaman --}}
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4 border-b border-white/10 pb-2">Rincian Finansial</h3>
                <div class="flex justify-between items-center">
                    <p class="text-xs text-slate-500">Nominal Pengajuan</p>
                    <p class="text-sm font-medium text-slate-200">{{ format_rupiah($pinjaman->nominal_pinjaman) }}</p>
                </div>
                <div class="flex justify-between items-center">
                    <p class="text-xs text-slate-500">Tenor</p>
                    <p class="text-sm font-medium text-slate-200">{{ $pinjaman->tenor_bulan }} Bulan</p>
                </div>
                <div class="flex justify-between items-center">
                    <p class="text-xs text-slate-500">Bunga ({{ format_persen($pinjaman->bunga_persen) }})</p>
                    <p class="text-sm font-medium text-slate-200">{{ format_rupiah($pinjaman->total_bunga) }}</p>
                </div>
                <div class="flex justify-between items-center pb-2 border-b border-white/5">
                    <p class="text-xs text-slate-500">Total Angsuran per Bulan</p>
                    <p class="text-sm font-bold text-white">{{ format_rupiah($pinjaman->total_angsuran) }}</p>
                </div>
                <div class="pt-2">
                    <p class="text-xs text-amber-500/80 mb-1 text-right">Potongan 5% di Muka: -{{ format_rupiah($pinjaman->total_potongan) }}</p>
                    <div class="flex justify-between items-center">
                        <p class="text-xs text-emerald-400/80">Dana Bersih Diterima</p>
                        <p class="text-lg font-bold text-emerald-400">{{ format_rupiah($pinjaman->dana_diterima) }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($pinjaman->catatan)
        <div class="p-6 bg-amber-500/5 border-t border-amber-500/10">
            <p class="text-xs text-amber-500/70 uppercase tracking-widest font-semibold mb-2">Catatan Pengurus</p>
            <p class="text-sm text-amber-100/80">{{ $pinjaman->catatan }}</p>
        </div>
        @endif
        
        <div class="p-4 border-t border-white/10 text-center bg-black/20">
            <a href="{{ route('pinjaman.guest.status') }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                &larr; Cek Pengajuan Lain
            </a>
        </div>
    </div>
</div>
@endsection
