@extends('layouts.app')

@section('title', 'Kewajiban & Realisasi Dana SHU')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-stone-800 tracking-tight">Kewajiban & Realisasi Dana SHU</h1>
            <p class="text-stone-500 text-sm mt-1">Kelola dana titipan SHU yang belum direalisasikan oleh koperasi.</p>
        </div>

        {{-- Filter Tahun --}}
        @if(count($tahunTersedia) > 0)
        <form method="GET" action="{{ route('shu.kewajiban.index') }}" class="flex items-center gap-2">
            <label class="text-sm font-medium text-stone-500">Tahun:</label>
            <select name="tahun" onchange="this.form.submit()" class="text-sm border border-stone-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] bg-white">
                <option value="">Semua Tahun</option>
                @foreach($tahunTersedia as $t)
                    <option value="{{ $t }}" {{ $tahunFilter == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </form>
        @endif
    </div>

    {{-- Ringkasan Kartu --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-stat-card
            title="Total Dana Dialokasikan"
            :value="'Rp ' . number_format($ringkasan['total_nominal_awal'], 0, ',', '.')"
            :subtitle="$ringkasan['jumlah_dompet'] . ' pos kewajiban'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'
        />
        <x-stat-card
            title="Total Terrealisasi"
            :value="'Rp ' . number_format($ringkasan['total_terpakai'], 0, ',', '.')"
            :subtitle="$ringkasan['jumlah_lunas'] . ' pos sudah lunas'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
        />
        <x-stat-card
            title="Saldo Tersisa"
            :value="'Rp ' . number_format($ringkasan['total_sisa'], 0, ',', '.')"
            subtitle="Menunggu realisasi"
            :highlight="$ringkasan['total_sisa'] > 0"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
        />
        <x-stat-card
            title="Jumlah Pos"
            :value="$ringkasan['jumlah_dompet']"
            :subtitle="$ringkasan['jumlah_lunas'] . ' lunas, ' . ($ringkasan['jumlah_dompet'] - $ringkasan['jumlah_lunas']) . ' berjalan'"
            icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>'
        />
    </div>

    {{-- Daftar Dompet Kewajiban --}}
    @if($kewajibanAll->isEmpty())
    <x-card class="text-center py-16">
        <svg class="w-16 h-16 text-stone-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        <p class="text-stone-500 text-base font-medium">Belum ada dana kewajiban dari SHU.</p>
        <p class="text-stone-400 text-sm mt-1">Dana kewajiban otomatis dibuat saat eksekusi distribusi SHU yang memiliki alokasi selain Jasa Modal, Jasa Usaha, Dana Pengurus, dan Dana Cadangan.</p>
    </x-card>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($kewajibanAll as $kewajiban)
        @php
            $persen = $kewajiban->nominal_awal > 0 ? round(($kewajiban->nominal_terpakai / $kewajiban->nominal_awal) * 100, 1) : 0;
            $isLunas = $kewajiban->saldo_tersisa <= 0;
        @endphp
        <a href="{{ route('shu.kewajiban.show', $kewajiban) }}" class="block group">
            <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 hover:shadow-md hover:border-[#043d2e]/30 transition-all duration-300 relative overflow-hidden h-full">
                {{-- Status Badge --}}
                @if($isLunas)
                <div class="absolute top-4 right-4">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Lunas
                    </span>
                </div>
                @else
                <div class="absolute top-4 right-4">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-700">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Berjalan
                    </span>
                </div>
                @endif

                {{-- Tahun --}}
                <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest mb-1">SHU Tahun {{ $kewajiban->tahun }}</p>

                {{-- Nama Alokasi --}}
                <h3 class="text-lg font-bold text-stone-800 group-hover:text-[#043d2e] transition-colors pr-20">{{ $kewajiban->nama_alokasi }}</h3>

                {{-- Nominal --}}
                <div class="mt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">Dialokasikan</span>
                        <span class="font-mono font-semibold text-stone-700">{{ format_rupiah($kewajiban->nominal_awal) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">Terrealisasi</span>
                        <span class="font-mono font-semibold text-emerald-600">{{ format_rupiah($kewajiban->nominal_terpakai) }}</span>
                    </div>
                    <div class="flex justify-between text-sm border-t border-stone-100 pt-2">
                        <span class="text-stone-600 font-semibold">Sisa</span>
                        <span class="font-mono font-bold {{ $isLunas ? 'text-emerald-600' : 'text-amber-600' }}">{{ format_rupiah($kewajiban->saldo_tersisa) }}</span>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="mt-4">
                    <div class="flex justify-between text-[11px] text-stone-400 mb-1.5">
                        <span>{{ $persen }}% direalisasikan</span>
                        <span>{{ $kewajiban->realisasi_count }} transaksi</span>
                    </div>
                    <div class="w-full bg-stone-100 rounded-full h-2 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-700 {{ $isLunas ? 'bg-emerald-500' : 'bg-[#043d2e]' }}" style="width: {{ min($persen, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif

</div>
@endsection
