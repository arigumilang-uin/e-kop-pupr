@extends('layouts.app')

@section('title', 'Periode Pinjaman')
@section('subtitle', 'Kelola periode pembukaan pinjaman & link pengajuan anggota koperasi.')

@section('actions')
    <div class="hidden sm:flex items-center gap-2.5 px-3 py-2 bg-stone-100 rounded-xl border border-stone-200/80 shadow-sm">
        <div class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#043d2e] opacity-40"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-[#043d2e]"></span>
        </div>
        <span class="text-sm font-medium text-stone-600">
            Total Periode: <span class="font-bold text-stone-900 ml-0.5">{{ $periodes->total() }}</span>
        </span>
    </div>

    <a href="{{ route('periode.create') }}" class="py-2.5 px-4 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Buka Periode Baru</span>
    </a>
@endsection

@section('content')
<div class="flex flex-col gap-5">
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm flex flex-col">
        <div class="p-5 border-b border-stone-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-stone-800">Daftar Periode Pinjaman</h3>
                <p class="text-[13px] text-stone-500 font-medium">Rekapitulasi seluruh periode pinjaman anggota koperasi.</p>
            </div>
            <div class="px-3 py-1.5 bg-stone-50 rounded-lg border border-stone-200">
                <span class="text-[13px] font-bold text-stone-600">{{ $periodes->total() }} Data</span>
            </div>
        </div>

        <div class="relative flex-1 flex flex-col">
            <div class="flex-1 flex flex-col justify-between">
                <x-table>
                    <x-table.thead>
                        <x-table.th>Nama Periode</x-table.th>
                        <x-table.th>Rentang Aktif</x-table.th>
                        <x-table.th class="text-center">Tahun</x-table.th>
                        <x-table.th class="text-right">Maks. /Pinjaman</x-table.th>
                        <x-table.th class="text-center">Total Pengajuan</x-table.th>
                        <x-table.th class="text-right">Aksi</x-table.th>
                    </x-table.thead>
                    <x-table.tbody>
                        @forelse($periodes as $periode)
                        <x-table.tr>
                            <x-table.td>
                                <div class="flex flex-col items-start gap-1">
                                    <span class="font-bold text-stone-800">{{ $periode->nama_periode }}</span>
                                    <span class="inline-flex items-center px-1.5 py-[1px] rounded text-[9px] font-bold uppercase tracking-wider
                                        @if($periode->statusEfektif()->value === 'buka') bg-emerald-50 text-emerald-600 border border-emerald-200
                                        @elseif($periode->statusEfektif()->value === 'terjadwal') bg-amber-50 text-amber-600 border border-amber-200
                                        @elseif($periode->statusEfektif()->value === 'tutup') bg-red-50 text-red-600 border border-red-200
                                        @else bg-stone-50 text-stone-600 border border-stone-200 @endif
                                    ">
                                        {{ $periode->statusEfektif()->label() }}
                                    </span>
                                </div>
                            </x-table.td>
                            <x-table.td>
                                <span class="font-medium text-[12px] text-stone-600">
                                    {{ $periode->tanggal_buka->format('d/m/Y') }} 
                                    @if($periode->tanggal_tutup)
                                        <span class="text-stone-300 mx-1">-</span> {{ $periode->tanggal_tutup->format('d/m/Y') }}
                                    @endif
                                </span>
                            </x-table.td>
                            <x-table.td class="text-center">
                                <span class="font-bold text-[12px] text-stone-600">
                                    {{ $periode->tahun }}
                                </span>
                            </x-table.td>
                            <x-table.td class="text-right">
                                <span class="font-mono text-[13px] font-semibold text-stone-700">
                                    {{ format_rupiah($periode->limit_per_anggota) }}
                                </span>
                            </x-table.td>
                            <x-table.td class="text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs font-bold font-mono border border-blue-200">
                                    {{ $periode->pinjaman_count }}
                                </span>
                            </x-table.td>
                            <x-table.td class="text-right">
                                <a href="{{ route('periode.show', $periode) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold text-[#043d2e] bg-[#043d2e]/5 hover:bg-[#043d2e]/10 border border-[#043d2e]/10 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    Kelola Periode
                                </a>
                            </x-table.td>
                        </x-table.tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-2xl bg-stone-50 border border-stone-200 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-stone-800 font-bold mb-1">Belum Ada Periode</h4>
                                    <p class="text-sm text-stone-500 mb-5">Buat periode pinjaman pertama agar anggota dapat mengajukan pinjaman secara online.</p>
                                    <a href="{{ route('periode.create') }}" class="py-2 px-4 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold transition-colors flex items-center gap-2 shadow-sm">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Buka Periode Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </x-table.tbody>
                </x-table>
            </div>
            
            @if($periodes->hasPages())
                <div class="px-5 py-4 border-t border-stone-200 bg-stone-50 rounded-b-2xl">
                    {{ $periodes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
