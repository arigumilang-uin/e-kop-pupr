@extends('layouts.app')

@section('title', $kewajiban->nama_alokasi . ' — Kewajiban SHU ' . $kewajiban->tahun)

@section('content')
<div class="space-y-6">

    {{-- Header + Back --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-start gap-4">
            <a href="{{ route('shu.kewajiban.index') }}" class="mt-1 shrink-0 w-10 h-10 rounded-xl bg-stone-100 border border-stone-200 flex items-center justify-center text-stone-500 hover:bg-stone-200 hover:text-stone-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <p class="text-[11px] font-bold text-stone-400 uppercase tracking-widest">SHU Tahun {{ $kewajiban->tahun }}</p>
                <h1 class="text-2xl font-extrabold text-stone-800 tracking-tight">{{ $kewajiban->nama_alokasi }}</h1>
                <p class="text-stone-500 text-sm mt-0.5">Detail & riwayat realisasi pengeluaran dana</p>
            </div>
        </div>
        @if($kewajiban->saldo_tersisa <= 0)
        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-bold bg-emerald-100 text-emerald-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            Dana Telah Lunas
        </span>
        @endif
    </div>

    {{-- Ringkasan Saldo --}}
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Dana Dialokasikan</p>
                <p class="text-2xl font-extrabold font-mono text-stone-800">{{ format_rupiah($kewajiban->nominal_awal) }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Sudah Direalisasi</p>
                <p class="text-2xl font-extrabold font-mono text-emerald-600">{{ format_rupiah($kewajiban->nominal_terpakai) }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-stone-400 uppercase tracking-wider mb-1">Saldo Tersisa</p>
                <p class="text-2xl font-extrabold font-mono {{ $kewajiban->saldo_tersisa > 0 ? 'text-amber-600' : 'text-emerald-600' }}">{{ format_rupiah($kewajiban->saldo_tersisa) }}</p>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="mt-6">
            <div class="flex justify-between text-xs text-stone-500 mb-2">
                <span>Realisasi {{ $persentaseTerpakai }}%</span>
                <span>{{ $kewajiban->realisasi->count() }} transaksi</span>
            </div>
            <div class="w-full bg-stone-100 rounded-full h-3 overflow-hidden">
                <div class="h-full rounded-full transition-all duration-1000 {{ $kewajiban->saldo_tersisa <= 0 ? 'bg-emerald-500' : 'bg-gradient-to-r from-[#043d2e] to-emerald-500' }}" style="width: {{ min($persentaseTerpakai, 100) }}%"></div>
            </div>
        </div>
    </x-card>

    {{-- Form Tambah Realisasi --}}
    @if($kewajiban->saldo_tersisa > 0)
    <div x-data="{ open: false }">
        <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200"
            :class="open ? 'bg-stone-800 text-white' : 'bg-[#043d2e] text-white hover:bg-[#032e22]'">
            <svg class="w-4 h-4 transition-transform duration-300" :class="{'rotate-45': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span x-text="open ? 'Tutup Form' : 'Catat Realisasi Pengeluaran'"></span>
        </button>

        <div x-show="open" x-collapse x-cloak class="mt-4">
            <x-card>
                <h3 class="text-base font-bold text-stone-800 mb-4">Realisasi Pengeluaran Baru</h3>
                <form method="POST" action="{{ route('shu.kewajiban.realisasi.store', $kewajiban) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-label for="tanggal_realisasi">Tanggal Realisasi</x-label>
                            <x-input type="date" name="tanggal_realisasi" id="tanggal_realisasi" required :value="old('tanggal_realisasi', date('Y-m-d'))" />
                            @error('tanggal_realisasi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-label for="nominal_realisasi">Nominal Pengeluaran</x-label>
                            <x-currency-input name="nominal" id="nominal_realisasi" required placeholder="Masukkan nominal" :value="old('nominal')" />
                            <p class="text-xs text-stone-400 mt-1">Maks: {{ format_rupiah($kewajiban->saldo_tersisa) }}</p>
                            @error('nominal') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <x-label for="keterangan_realisasi">Keterangan</x-label>
                            <x-input type="text" name="keterangan" id="keterangan_realisasi" required placeholder="Misal: Sumbangan sembako banjir..." :value="old('keterangan')" />
                            @error('keterangan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end mt-4">
                        <x-button type="submit">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Realisasi
                        </x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
    @endif

    {{-- Riwayat Realisasi --}}
    <x-card>
        <h3 class="text-base font-bold text-stone-800 mb-4">
            Riwayat Realisasi Pengeluaran
            <span class="text-stone-400 font-normal text-sm ml-1">({{ $kewajiban->realisasi->count() }})</span>
        </h3>

        @if($kewajiban->realisasi->isEmpty())
        <div class="text-center py-12">
            <svg class="w-12 h-12 text-stone-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-stone-400 text-sm">Belum ada realisasi pengeluaran untuk dana ini.</p>
        </div>
        @else
        <x-table>
            <x-table.thead :sticky="false">
                <x-table.th class="w-12">No</x-table.th>
                <x-table.th>Tanggal</x-table.th>
                <x-table.th>Keterangan</x-table.th>
                <x-table.th class="text-right">Nominal</x-table.th>
                <x-table.th>Dicatat Oleh</x-table.th>
                <x-table.th class="text-center w-20">Aksi</x-table.th>
            </x-table.thead>
            <x-table.tbody>
                @foreach($kewajiban->realisasi as $i => $r)
                <x-table.tr>
                    <x-table.td class="text-stone-400 font-mono text-xs">{{ $i + 1 }}</x-table.td>
                    <x-table.td>
                        <span class="font-medium text-stone-700">{{ $r->tanggal_realisasi->translatedFormat('d M Y') }}</span>
                    </x-table.td>
                    <x-table.td>
                        <span class="text-stone-700">{{ $r->keterangan }}</span>
                    </x-table.td>
                    <x-table.td class="text-right">
                        <span class="font-mono font-semibold text-red-600">- {{ format_rupiah($r->nominal) }}</span>
                    </x-table.td>
                    <x-table.td>
                        <span class="text-stone-500 text-xs">{{ $r->pencatat?->nama ?? '-' }}</span>
                    </x-table.td>
                    <x-table.td class="text-center">
                        <form method="POST" action="{{ route('shu.kewajiban.realisasi.destroy', [$kewajiban, $r]) }}" onsubmit="return confirm('Hapus realisasi ini? Saldo akan dikembalikan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-stone-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus & Rollback">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </x-table.td>
                </x-table.tr>
                @endforeach
            </x-table.tbody>
            <tfoot class="bg-stone-50 border-t border-stone-200">
                <tr>
                    <td colspan="3" class="px-5 py-4 text-sm font-bold text-stone-600">Total Realisasi</td>
                    <td class="px-5 py-4 text-right">
                        <span class="font-mono font-extrabold text-red-600">- {{ format_rupiah($kewajiban->nominal_terpakai) }}</span>
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </x-table>
        @endif
    </x-card>

</div>
@endsection
