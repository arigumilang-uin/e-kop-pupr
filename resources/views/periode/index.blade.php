@extends('layouts.app')

@section('title', 'Periode Pinjaman')
@section('subtitle', 'Kelola periode pembukaan pinjaman & link pengajuan anggota')

@section('actions')
<a href="{{ route('periode.create') }}"
   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium
          hover:bg-blue-500 transition-all shadow-sm hover:shadow-md">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
    </svg>
    Buka Periode Baru
</a>
@endsection

@section('content')
@if($periodes->isEmpty())
<div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
    <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    </div>
    <h3 class="text-lg font-semibold text-slate-800 mb-1">Belum ada Periode</h3>
    <p class="text-sm text-slate-500 mb-5">Buat periode pinjaman pertama agar anggota dapat mengajukan pinjaman.</p>
    <a href="{{ route('periode.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-500 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buka Periode Baru
    </a>
</div>
@else
<div class="space-y-4">
    @foreach($periodes as $periode)
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden">
        <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-2">
                    <h3 class="text-base font-semibold text-slate-800 truncate">{{ $periode->nama_periode }}</h3>
                    <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium
                        @if($periode->statusEfektif()->value === 'buka') bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20
                        @elseif($periode->statusEfektif()->value === 'terjadwal') bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20
                        @elseif($periode->statusEfektif()->value === 'tutup') bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20
                        @else bg-slate-50 text-slate-600 ring-1 ring-inset ring-slate-500/20 @endif
                    ">
                        {{ $periode->statusEfektif()->label() }}
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-x-5 gap-y-1 text-sm text-slate-500">
                    <span class="inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Tahun {{ $periode->tahun }}
                    </span>
                    <span>Buka: {{ $periode->tanggal_buka->format('d/m/Y') }}</span>
                    @if($periode->tanggal_tutup)
                    <span>Tutup: {{ $periode->tanggal_tutup->format('d/m/Y') }}</span>
                    @endif
                    <span>Limit: {{ format_rupiah($periode->limit_per_anggota) }}</span>
                    <span class="font-medium text-slate-700">{{ $periode->pinjaman_count }} pengajuan</span>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('periode.show', $periode) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Detail & Link
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-6">
    {{ $periodes->links() }}
</div>
@endif
@endsection
