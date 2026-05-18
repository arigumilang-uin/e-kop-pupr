@extends('layouts.app')

@section('title', 'Ringkasan Keuangan')
@section('subtitle', 'Ikhtisar kondisi keuangan dan perputaran arus kas Koperasi')

@section('actions')
<a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 py-2.5 px-4 rounded-xl bg-white border border-stone-200 text-stone-600 hover:bg-stone-50 hover:text-stone-900 text-sm font-bold transition-all shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
    Kembali ke Dashboard
</a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-xl border border-stone-200 shadow-sm p-1.5 flex flex-wrap gap-1">
        @php
            $tabs = [
                'ringkasan' => ['label' => 'Ringkasan Aset', 'icon' => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3'],
                'piutang' => ['label' => 'Piutang Koperasi', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                'simpanan' => ['label' => 'Simpanan Anggota', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                'kas' => ['label' => 'Arus Kas', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp
        @foreach($tabs as $key => $t)
        <a href="{{ route('keuangan.laporan', ['tab' => $key]) }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-bold transition-all {{ $tab === $key ? 'bg-[#043d2e] text-white shadow-sm' : 'text-stone-500 hover:bg-stone-50 hover:text-stone-800' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $t['icon'] }}"/></svg>
            {{ $t['label'] }}
        </a>
        @endforeach
    </div>

    {{-- ======================== --}}
    {{-- TAB: RINGKASAN ASET     --}}
    {{-- ======================== --}}
    @if($tab === 'ringkasan')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-[#043d2e] rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
            <div class="relative z-10">
                <p class="text-emerald-100/80 text-sm font-bold mb-1 uppercase tracking-widest">Kas Tersedia</p>
                <p class="text-3xl font-black font-mono mt-2 mb-1">{{ format_rupiah($ringkasan['saldoKoperasi']) }}</p>
                <p class="text-emerald-200/60 text-[11px]">Dana liquid di rekening koperasi</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5">
                <svg class="w-16 h-16 text-amber-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            </div>
            <div class="relative z-10">
                <p class="text-stone-500 text-xs font-bold mb-1 uppercase tracking-widest">Piutang Beredar</p>
                <p class="text-2xl font-black font-mono text-stone-800 mt-2 mb-1">{{ format_rupiah($ringkasan['piutangBerjalan']) }}</p>
                <p class="text-stone-400 text-[11px]">Sisa pokok pinjaman berjalan</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5">
                <svg class="w-16 h-16 text-violet-600" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H4V6h16v12zM6 10h2v2H6zm0 4h8v2H6zm10 0h2v2h-2zm-6-4h8v2h-8z"/></svg>
            </div>
            <div class="relative z-10">
                <p class="text-stone-500 text-xs font-bold mb-1 uppercase tracking-widest">Titipan Simpanan</p>
                <p class="text-2xl font-black font-mono text-stone-800 mt-2 mb-1">{{ format_rupiah($ringkasan['simpananBersih']) }}</p>
                <p class="text-stone-400 text-[11px]">Total akumulasi simpanan anggota</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5">
                <svg class="w-16 h-16 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M11.67 3.87 9.9 2.1 3 9l6.9 6.9 1.77-1.77L5.83 9l5.84-5.13zm6.41 12.26 1.77 1.77L21 9l-6.9-6.9-1.77 1.77L18.17 9l-5.84 5.13z"/></svg>
            </div>
            <div class="relative z-10">
                <p class="text-stone-500 text-xs font-bold mb-1 uppercase tracking-widest">Piutang Lain-Lain</p>
                <p class="text-2xl font-black font-mono text-stone-800 mt-2 mb-1">{{ format_rupiah($ringkasan['piutangLain']) }}</p>
                <p class="text-stone-400 text-[11px]">Legacy (Tahun 2024/2025)</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-stone-200 shadow-sm relative overflow-hidden">
            <div class="absolute inset-x-0 bottom-0 h-1 bg-emerald-500"></div>
            <div class="relative z-10">
                <p class="text-stone-500 text-xs font-bold mb-1 uppercase tracking-widest">Estimasi Aset</p>
                <p class="text-2xl font-black font-mono text-emerald-600 mt-2 mb-1">{{ format_rupiah($ringkasan['totalAset']) }}</p>
                <p class="text-stone-400 text-[11px]">Kas + Piutang Berjalan + Piutang Lain</p>
            </div>
        </div>
    </div>

    {{-- Komposisi Aset --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-8">
            <h3 class="text-sm font-black text-stone-800 uppercase tracking-widest mb-6 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                </div>
                Komposisi Kekayaan
            </h3>
            @php
                $asetTotal = $ringkasan['totalAset'] > 0 ? $ringkasan['totalAset'] : 1;
                $pctKas = round(($ringkasan['saldoKoperasi'] / $asetTotal) * 100, 1);
                $pctPiutang = round(($ringkasan['piutangBerjalan'] / $asetTotal) * 100, 1);
            @endphp
            <div class="space-y-5">
                <div>
                    <div class="flex justify-between items-baseline mb-2">
                        <span class="text-sm font-bold text-stone-700">Kas Saldo (Liquid)</span>
                        <div class="text-right">
                            <span class="font-mono font-black text-stone-800">{{ format_rupiah($ringkasan['saldoKoperasi']) }}</span>
                            <span class="inline-block ml-2 px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded">{{ $pctKas }}%</span>
                        </div>
                    </div>
                    <div class="w-full bg-stone-100 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $pctKas }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-baseline mb-2">
                        <span class="text-sm font-bold text-stone-700">Piutang Pokok Beredar</span>
                        <div class="text-right">
                            <span class="font-mono font-black text-stone-800">{{ format_rupiah($ringkasan['piutangBerjalan']) }}</span>
                            <span class="inline-block ml-2 px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded">{{ $pctPiutang }}%</span>
                        </div>
                    </div>
                    <div class="w-full bg-stone-100 rounded-full h-2">
                        <div class="bg-amber-400 h-2 rounded-full" style="width: {{ $pctPiutang }}%"></div>
                    </div>
                <div>
                    @php $pctPiutangLain = round(($ringkasan['piutangLain'] / $asetTotal) * 100, 1); @endphp
                    <div class="flex justify-between items-baseline mb-2">
                        <span class="text-sm font-bold text-stone-700">Piutang Lain-Lain (Legacy)</span>
                        <div class="text-right">
                            <span class="font-mono font-black text-stone-800">{{ format_rupiah($ringkasan['piutangLain']) }}</span>
                            <span class="inline-block ml-2 px-2 py-0.5 bg-violet-50 text-violet-700 text-[10px] font-bold rounded">{{ $pctPiutangLain }}%</span>
                        </div>
                    </div>
                    <div class="w-full bg-stone-100 rounded-full h-2">
                        <div class="bg-violet-400 h-2 rounded-full" style="width: {{ $pctPiutangLain }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-8">
            <h3 class="text-sm font-black text-stone-800 uppercase tracking-widest mb-6 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center border border-violet-100">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                Komposisi Simpanan Anggota
            </h3>
            <div class="space-y-4">
                @foreach($ringkasan['simpananPerJenis'] as $sim)
                @php $pct = $ringkasan['simpananBersih'] > 0 ? round(($sim->total / $ringkasan['simpananBersih']) * 100, 1) : 0; @endphp
                <div class="flex items-center gap-4">
                    <div class="w-full">
                        <div class="flex justify-between items-baseline mb-1">
                            <span class="text-xs font-bold text-stone-600">{{ $sim->nama }}</span>
                            <span class="font-mono text-xs font-black text-stone-800">{{ format_rupiah($sim->total) }} <span class="text-stone-400 font-normal ml-1">{{ $pct }}%</span></span>
                        </div>
                        <div class="w-full bg-stone-100 rounded-full h-1.5">
                            <div class="bg-[#043d2e] h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ======================== --}}
    {{-- TAB: PIUTANG KOPERASI   --}}
    {{-- ======================== --}}
    @if($tab === 'piutang')
    @php 
        $totalPiutangAll = $piutangAnggota->sum('total_belum'); 
        $totalPokok = $piutangAnggota->sum('total_belum_pokok');
        $totalBunga = $piutangAnggota->sum('total_belum_bunga');
        $jumlahPeminjam = $piutangGrouped->count();
        $jumlahPinjaman = $piutangAnggota->count();
    @endphp
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <h2 class="text-2xl font-black text-stone-800 mb-2">Ringkasan Piutang</h2>
            <p class="text-stone-500 text-sm">Informasi global mengenai pinjaman anggota yang masih berstatus berjalan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-6 bg-stone-50 rounded-2xl border border-stone-100 text-center">
                <p class="text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Total Tagihan</p>
                <p class="text-2xl font-black font-mono text-stone-800">{{ format_rupiah($totalPiutangAll) }}</p>
                <p class="text-[11px] text-stone-400 mt-2">Pokok + Bunga Berjalan</p>
            </div>
            <div class="p-6 bg-stone-50 rounded-2xl border border-stone-100 text-center">
                <p class="text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Piutang Pokok</p>
                <p class="text-2xl font-black font-mono text-[#043d2e]">{{ format_rupiah($totalPokok) }}</p>
                <p class="text-[11px] text-stone-400 mt-2">Modal Koperasi Beredar</p>
            </div>
            <div class="p-6 bg-stone-50 rounded-2xl border border-stone-100 text-center">
                <p class="text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Estimasi Bunga</p>
                <p class="text-2xl font-black font-mono text-amber-600">{{ format_rupiah($totalBunga) }}</p>
                <p class="text-[11px] text-stone-400 mt-2">Proyeksi Pendapatan Ke Depan</p>
            </div>
            <div class="p-6 bg-stone-50 rounded-2xl border border-stone-100 text-center">
                <p class="text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Pinjaman Aktif</p>
                <p class="text-2xl font-black font-mono text-stone-800">{{ $jumlahPinjaman }}</p>
                <p class="text-[11px] text-stone-400 mt-2">Dari {{ $jumlahPeminjam }} Anggota Peminjam</p>
            </div>
        </div>

        <div class="mt-8 p-6 bg-amber-50 rounded-2xl border border-amber-100 flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-amber-900 mb-1">Catatan Piutang</h4>
                <p class="text-xs text-amber-700/80 leading-relaxed">
                    Angka piutang pokok merupakan modal primer koperasi yang sedang dikelola oleh anggota. Estimasi bunga belum diakui sebagai pendapatan riil sampai angsuran benar-benar dibayarkan.
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- ======================== --}}
    {{-- TAB: SIMPANAN ANGGOTA   --}}
    {{-- ======================== --}}
    @if($tab === 'simpanan')
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <h2 class="text-2xl font-black text-stone-800 mb-2">Ringkasan Simpanan</h2>
            <p class="text-stone-500 text-sm">Akumulasi seluruh simpanan anggota koperasi yang diakui sebagai Titipan atau Kewajiban Koperasi.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($ringkasan['simpananPerJenis'] as $sim)
            <div class="flex items-center gap-4 p-5 bg-stone-50 rounded-2xl border border-stone-100 transition-all hover:border-stone-200 hover:shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-white border border-stone-200 flex items-center justify-center shadow-sm shrink-0">
                    <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-stone-500 uppercase tracking-widest mb-1">{{ $sim->nama }}</p>
                    <p class="text-lg font-black font-mono text-stone-800">{{ format_rupiah($sim->total) }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="flex flex-col md:flex-row items-center justify-between p-6 bg-[#043d2e] rounded-2xl relative overflow-hidden text-white shadow-lg">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
            <div class="relative z-10">
                <p class="text-emerald-100/80 text-xs font-bold uppercase tracking-widest mb-1">Total Simpanan Keseluruhan</p>
                <p class="text-3xl font-black font-mono">{{ format_rupiah($ringkasan['simpananBersih']) }}</p>
            </div>
            <div class="relative z-10 text-right mt-4 md:mt-0">
                <p class="text-emerald-100/80 text-xs font-bold uppercase tracking-widest mb-1">Anggota Terdaftar</p>
                <p class="text-2xl font-black font-mono">{{ $simpananGrouped->count() }} <span class="text-sm font-normal">Akun</span></p>
            </div>
        </div>
    </div>
    @endif

    {{-- ======================== --}}
    {{-- TAB: ARUS KAS           --}}
    {{-- ======================== --}}
    @if($tab === 'kas')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Dana Masuk --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-8">
            <h3 class="text-sm font-black text-emerald-700 uppercase tracking-widest mb-6 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center border border-emerald-100">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
                Pemasukan Kas
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-4 bg-stone-50 rounded-xl border border-stone-100 transition-colors hover:bg-emerald-50/50">
                    <div>
                        <p class="text-sm font-bold text-stone-700">Setoran Simpanan Masuk</p>
                        <p class="text-[11px] text-stone-500 mt-0.5">Seluruh simpanan anggota yang masuk ke kas</p>
                    </div>
                    <span class="font-mono font-black text-emerald-700">{{ format_rupiah($ringkasan['masukSimpanan']) }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-stone-50 rounded-xl border border-stone-100 transition-colors hover:bg-emerald-50/50">
                    <div>
                        <p class="text-sm font-bold text-stone-700">Pembayaran Angsuran</p>
                        <p class="text-[11px] text-stone-500 mt-0.5">Pokok + bunga dari angsuran pinjaman terbayar</p>
                    </div>
                    <span class="font-mono font-black text-emerald-700">{{ format_rupiah($ringkasan['masukAngsuran']) }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-stone-50 rounded-xl border border-stone-100 transition-colors hover:bg-emerald-50/50">
                    <div>
                        <p class="text-sm font-bold text-stone-700">Pembayaran Piutang Lain-Lain</p>
                        <p class="text-[11px] text-stone-500 mt-0.5">Pengembalian piutang legacy/pihak ketiga</p>
                    </div>
                    <span class="font-mono font-black text-emerald-700">{{ format_rupiah($ringkasan['masukPiutangLain']) }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-stone-50 rounded-xl border border-stone-100 transition-colors hover:bg-emerald-50/50">
                    <div>
                        <p class="text-sm font-bold text-stone-700">Pendapatan Fee & Admin</p>
                        <p class="text-[11px] text-stone-500 mt-0.5">Potongan dana resiko & biaya administrasi pinjaman</p>
                    </div>
                    <span class="font-mono font-black text-emerald-700">{{ format_rupiah($ringkasan['masukFee']) }}</span>
                </div>
                <div class="pt-4 mt-2 border-t-2 border-stone-100 flex justify-between items-center px-2">
                    <span class="font-black text-stone-800 uppercase tracking-widest text-xs">Total Pemasukan</span>
                    <span class="font-mono text-lg font-black text-emerald-600">{{ format_rupiah($ringkasan['masukSimpanan'] + $ringkasan['masukAngsuran'] + $ringkasan['masukPiutangLain'] + $ringkasan['masukFee']) }}</span>
                </div>
            </div>
        </div>

        {{-- Dana Keluar --}}
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-8">
            <h3 class="text-sm font-black text-red-700 uppercase tracking-widest mb-6 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center border border-red-100">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
                Pengeluaran Kas
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-4 bg-stone-50 rounded-xl border border-stone-100 transition-colors hover:bg-red-50/50">
                    <div>
                        <p class="text-sm font-bold text-stone-700">Pencairan Pinjaman Baru</p>
                        <p class="text-[11px] text-stone-500 mt-0.5">Nominal pinjaman dicairkan (sebelum dipotong admin)</p>
                    </div>
                    <span class="font-mono font-black text-red-600">{{ format_rupiah($ringkasan['keluarPinjaman']) }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-stone-50 rounded-xl border border-stone-100 transition-colors hover:bg-red-50/50">
                    <div>
                        <p class="text-sm font-bold text-stone-700">Penarikan Dana Anggota</p>
                        <p class="text-[11px] text-stone-500 mt-0.5">Keluarnya dana simpanan karena ditarik / anggota keluar</p>
                    </div>
                    <span class="font-mono font-black text-red-600">{{ format_rupiah($ringkasan['keluarTarik']) }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-stone-50 rounded-xl border border-stone-100 transition-colors hover:bg-red-50/50">
                    <div>
                        <p class="text-sm font-bold text-stone-700">Beban Operasional Kas</p>
                        <p class="text-[11px] text-stone-500 mt-0.5">Total belanja, honor, atau beban pengeluaran lainnya</p>
                    </div>
                    <span class="font-mono font-black text-red-600">{{ format_rupiah($ringkasan['keluarPengeluaranKas']) }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-stone-50 rounded-xl border border-stone-100 transition-colors hover:bg-red-50/50">
                    <div>
                        <p class="text-sm font-bold text-stone-700">Realisasi Dana SHU</p>
                        <p class="text-[11px] text-stone-500 mt-0.5">Pengeluaran untuk Dana Sosial, Pengurus, dsb.</p>
                    </div>
                    <span class="font-mono font-black text-red-600">{{ format_rupiah($ringkasan['keluarRealisasiShu']) }}</span>
                </div>
                <div class="pt-4 mt-2 border-t-2 border-stone-100 flex justify-between items-center px-2">
                    <span class="font-black text-stone-800 uppercase tracking-widest text-xs">Total Pengeluaran</span>
                    <span class="font-mono text-lg font-black text-red-600">{{ format_rupiah($ringkasan['keluarPinjaman'] + $ringkasan['keluarTarik'] + $ringkasan['keluarPengeluaranKas'] + $ringkasan['keluarRealisasiShu']) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Net Summary --}}
    <div class="bg-[#043d2e] rounded-2xl p-8 text-white shadow-lg relative overflow-hidden mt-6">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="text-center md:text-left">
                <p class="text-emerald-100/80 text-xs font-bold uppercase tracking-widest mb-2">Saldo Bersih Kas Koperasi</p>
                <p class="text-xs text-emerald-100/60 max-w-sm leading-relaxed">Merupakan akumulasi dari (Total Pemasukan − Total Pengeluaran), dan merupakan kas liquid yang saat ini tersedia dan siap digunakan oleh koperasi.</p>
            </div>
            <div class="text-center md:text-right">
                <p class="text-4xl font-black font-mono drop-shadow-md">{{ format_rupiah($ringkasan['saldoKoperasi']) }}</p>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
