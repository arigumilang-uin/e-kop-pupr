@extends('layouts.app')

@section('title', 'Manajemen Persetujuan Pinjaman')
@section('subtitle', 'Daftar pengajuan pinjaman anggota menunggu validasi kas dan approval')

@section('actions')
@endsection

@section('content')

<div x-data="pinjamanFilter()" class="flex flex-col gap-5">
    {{-- Filter Sticky Bar Component --}}
    <x-filter-bar searchPlaceholder="Cari NIP atau Nama..." x-model="q">
        <x-slot name="trailing">
            <div class="flex items-center gap-2.5 px-4 py-2.5 bg-white border border-stone-200 rounded-xl shadow-sm">
                <div class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#043d2e] opacity-40"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-[#043d2e]"></span>
                </div>
                <span class="text-sm font-medium text-stone-600">
                    Total Kas: <span class="font-bold text-stone-900 ml-0.5">Rp {{ number_format($totalSaldoKoperasi, 0, ',', '.') }}</span>
                </span>
            </div>
        </x-slot>

        <x-slot name="indicator">
            <template x-if="activeFiltersCount > 0">
                <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full shadow-sm"></span>
            </template>
        </x-slot>

        <x-slot name="filters">
            {{-- Filter Bidang --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Bidang Kerja</label>
                <select x-model="bidang_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Bidang</option>
                    @foreach($bidangs ?? [] as $b)
                        <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Filter Periode --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Periode Pinjaman</label>
                <select x-model="periode_id" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Periode</option>
                    @foreach($periodes ?? [] as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_periode }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Status --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Status Pinjaman</label>
                <select x-model="status" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Status</option>
                    @foreach(\App\Enums\StatusPinjaman::cases() as $s)
                        <option value="{{ $s->value }}">{{ ucfirst($s->value) }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Filter Indikator Khusus --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Indikator Khusus</label>
                <select x-model="indikator" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                    <option value="">Semua Pengajuan</option>
                    <option value="ganda">Terdapat Pengajuan Ganda</option>
                    <option value="aktif">Terdapat Pinjaman Aktif (Tahun ini)</option>
                </select>
            </div>
            
            {{-- Tombol Reset Filter --}}
            <template x-if="activeFiltersCount > 0">
                <button type="button" @click="bidang_id = ''; periode_id = ''; status = ''; indikator = ''; q = ''" class="mt-2 w-full py-2.5 px-4 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 text-sm font-bold transition-colors">
                    Reset Semua Filter
                </button>
            </template>
        </x-slot>
    </x-filter-bar>

    <div class="relative flex-1 flex flex-col">
        {{-- Loading Overlay --}}
        <div x-show="loading" 
             x-transition.opacity.duration.200ms
             class="absolute inset-0 bg-white/70 backdrop-blur-[2px] z-10 flex items-start justify-center pt-24" 
             style="display: none;">
            <div class="flex items-center gap-3 px-5 py-3 bg-white border border-stone-200 rounded-2xl shadow-xl text-sm font-bold text-stone-700">
                <svg class="animate-spin h-5 w-5 text-[#043d2e]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memuat data...
            </div>
        </div>

        <div id="table-content-container" class="flex-1 flex flex-col justify-between">

<div x-data="{
    selected: [],
    menungguIds: [{{ $pinjamans->filter(fn($p) => $p->status->value === 'menunggu')->pluck('id')->join(',') }}],
    get allSelected() { return this.selected.length > 0 && this.selected.length === this.menungguIds.length; },
    toggleAll() { if (this.allSelected) this.selected = []; else this.selected = [...this.menungguIds]; },
    showRejectModal: false
}">

    {{-- Alert Bar Mass Action --}}
    <div x-show="selected.length > 0" x-transition class="mb-6 bg-[#043d2e] rounded-2xl p-4 md:p-5 flex flex-col md:flex-row items-center justify-between gap-4 shadow-xl border border-[#043d2e]">
        <div class="flex items-center gap-4 text-white">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center font-black text-lg border border-white/20">
                <span x-text="selected.length"></span>
            </div>
            <div>
                <p class="font-bold text-sm tracking-wide">Pengajuan Terpilih</p>
                <p class="text-[11px] text-white/70 font-medium">Siap untuk dieksekusi secara massal</p>
            </div>
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('pinjaman.massApprove') }}" method="POST" class="w-full md:w-auto">
                @csrf
                <template x-for="id in selected" :key="id">
                    <input type="hidden" name="pinjaman_ids[]" :value="id">
                </template>
                <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-white text-[#043d2e] hover:bg-stone-50 text-sm font-black transition-all shadow-md active:scale-95 whitespace-nowrap" onclick="return confirm('Apakah Anda yakin memberikan persetujuan MASAL pada ' + selected.length + ' pengajuan ini?')">
                    Setujui Semua
                </button>
            </form>
            
            <button type="button" @click="showRejectModal = true" class="w-full md:w-auto px-5 py-2.5 rounded-xl bg-red-500 hover:bg-red-600 text-white text-sm font-black transition-all shadow-md active:scale-95 whitespace-nowrap border border-red-400">
                Tolak
            </button>
        </div>
    </div>

<div class="bg-white rounded-2xl border border-stone-200 shadow-sm flex flex-col">
    
    {{-- Legend Keterangan Status --}}
    <div class="bg-stone-50 border-b border-stone-200 px-6 py-3 flex items-center gap-5 text-[10px] font-bold uppercase tracking-widest text-stone-500 overflow-x-auto rounded-t-2xl">
        <div class="flex items-center gap-1.5 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Menunggu</div>
        <div class="flex items-center gap-1.5 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span> Berjalan</div>
        <div class="flex items-center gap-1.5 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Lunas</div>
        <div class="flex items-center gap-1.5 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Ditolak</div>
        <div class="flex items-center gap-1.5 whitespace-nowrap"><span class="w-2.5 h-2.5 rounded-full bg-stone-300"></span> Batal</div>
    </div>

    {{-- Table --}}
    <div class="relative flex-1 flex flex-col">
        <x-table>
            <x-table.thead :sticky="true" class="top-[168px]">
                <x-table.th class="w-12 text-center border-r border-stone-100">
                    <input type="checkbox" @click="toggleAll()" :checked="allSelected" :disabled="menungguIds.length === 0" class="w-4 h-4 rounded text-[#043d2e] focus:ring-[#043d2e]/20 border-stone-300 disabled:opacity-50 cursor-pointer">
                </x-table.th>
                <x-table.th>Referensi / Periode</x-table.th>
                <x-table.th>Anggota / Bidang</x-table.th>
                <x-table.th>Dana Cair & Potongan</x-table.th>
                <x-table.th>Plafon / Tenor</x-table.th>
                <x-table.th class="text-center">Status</x-table.th>
                <x-table.th class="text-right">Aksi</x-table.th>
            </x-table.thead>
            <x-table.tbody>
                @forelse($pinjamans as $pinjaman)
                <x-table.tr class="hover:bg-stone-50/50 transition-colors" x-bind:class="{ 'bg-stone-50': selected.includes({{ $pinjaman->id }}) }">
                    <x-table.td class="text-center border-r border-stone-100">
                        @if($pinjaman->status->value === 'menunggu')
                        <input type="checkbox" x-model="selected" value="{{ $pinjaman->id }}" class="w-4 h-4 rounded text-[#043d2e] focus:ring-[#043d2e]/20 border-stone-300 cursor-pointer">
                        @else
                        <div class="w-4 h-4 rounded border border-stone-200 bg-stone-100 opacity-50 inline-block"></div>
                        @endif
                    </x-table.td>
                    <x-table.td class="align-top">
                        <div class="flex flex-col gap-1">
                            <span class="font-mono text-[#043d2e] font-bold text-[13px]">{{ $pinjaman->no_referensi }}</span>
                            <span class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">{{ optional($pinjaman->periodePinjaman)->nama_periode ?? '-' }}</span>
                            <span class="text-[10px] font-medium text-stone-400">{{ $pinjaman->tanggal_pengajuan->format('d/m/Y') }}</span>
                        </div>
                    </x-table.td>
                    <x-table.td class="align-top">
                        <div class="flex flex-col gap-0.5">
                            <span class="text-stone-800 font-bold text-[13px]">{{ $pinjaman->anggota->nama }}</span>
                            <span class="text-[11px] font-medium text-stone-500">NIP: <x-nip-display :value="$pinjaman->anggota->nip" /></span>
                            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest mt-1">{{ optional($pinjaman->anggota->bidang)->nama_bidang ?? '-' }}</span>

                            @if($pinjaman->status->value === 'menunggu')
                                <div class="flex flex-col gap-1.5 mt-2.5">
                                    @if($pinjaman->anggota->pinjaman_aktif_tahun_ini > 0)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded text-[9px] font-bold uppercase tracking-wider w-max" title="Anggota ini memiliki pinjaman yang sedang berjalan di tahun ini">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        Ada Pinjaman Aktif
                                    </span>
                                    @endif
                                    
                                    @if($pinjaman->anggota->pinjaman_menunggu > 1)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-red-50 text-red-700 border border-red-200 rounded text-[9px] font-bold uppercase tracking-wider w-max" title="Anggota ini mengajukan pinjaman lebih dari 1 kali (menunggu approval)">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Pengajuan Ganda ({{ $pinjaman->anggota->pinjaman_menunggu }})
                                    </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </x-table.td>
                    <x-table.td class="align-top">
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center justify-between border-b border-stone-100 pb-1">
                                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Cair</span>
                                <span class="font-mono text-emerald-700 font-black text-[13px]">Rp {{ number_format($pinjaman->dana_diterima, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-red-500 uppercase tracking-widest">Potongan Awal</span>
                                <span class="font-mono text-red-600 font-medium text-[11px]">-Rp {{ number_format($pinjaman->total_potongan, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="px-1.5 py-0.5 rounded bg-stone-100 text-stone-600 font-bold text-[9px] uppercase tracking-wider border border-stone-200">{{ $pinjaman->nama_bank }}</span>
                                <span class="font-mono text-stone-500 text-[11px] truncate w-24" title="{{ $pinjaman->no_rekening }} - {{ $pinjaman->nama_rekening }}">{{ $pinjaman->no_rekening }}</span>
                            </div>
                        </div>
                    </x-table.td>
                    <x-table.td class="align-top whitespace-nowrap">
                        <div class="flex flex-col gap-0.5">
                            <span class="font-black text-stone-800 font-mono text-[13px]">Rp {{ number_format($pinjaman->nominal_pinjaman, 0, ',', '.') }}</span>
                            <span class="text-[11px] font-bold text-stone-500">{{ $pinjaman->tenor_bulan }} Bulan @ {{ number_format($pinjaman->bunga_persen, 1) }}%</span>
                        </div>
                    </x-table.td>
                    <x-table.td class="text-center align-top">
                        @php
                            $bgStatus = match($pinjaman->status->value) {
                                'menunggu' => 'bg-amber-400',
                                'berjalan' => 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]',
                                'lunas' => 'bg-blue-500',
                                'ditolak' => 'bg-red-500',
                                'dibatalkan' => 'bg-stone-300',
                                default => 'bg-stone-300'
                            };
                        @endphp
                        <div class="flex items-center justify-center p-2">
                            <span class="w-3 h-3 rounded-full {{ $bgStatus }}" title="{{ ucfirst($pinjaman->status->value) }}"></span>
                        </div>
                    </x-table.td>
                    <x-table.td class="text-right align-top">
                        <div class="flex items-center justify-end gap-1">
                            <x-action-dropdown>
                                <x-action-dropdown-item href="{{ route('pinjaman.show', $pinjaman->id) }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>'>
                                    Tinjau Pengajuan
                                </x-action-dropdown-item>
                            </x-action-dropdown>
                        </div>
                    </x-table.td>
                </x-table.tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 rounded-2xl bg-stone-50 border border-stone-200 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-stone-800 font-bold mb-1">Data Tidak Ditemukan</h4>
                            <p class="text-sm text-stone-500">Belum ada riwayat pengajuan pinjaman yang cocok.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </x-table.tbody>
        </x-table>
        
        @if($pinjamans->hasPages())
        <div class="px-6 py-4 border-t border-stone-200 bg-stone-50/30">
            <div class="alpine-pagination" @click.prevent="if($event.target.tagName === 'A' || $event.target.closest('a')) { let link = $event.target.tagName === 'A' ? $event.target : $event.target.closest('a'); if(link.href) { $dispatch('pinjaman-paginate', { url: link.href }) } }">
                {{ $pinjamans->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

    {{-- Modal Mass Reject --}}
    <div x-show="showRejectModal" x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center bg-stone-900/60 backdrop-blur-sm p-4" style="display: none;">
        <div @click.away="showRejectModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-stone-200 overflow-hidden transform transition-all">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 border border-red-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-stone-900">Tolak Secara Massal</h3>
                        <p class="text-[12px] font-medium text-stone-500">Akan menolak <span x-text="selected.length" class="font-bold text-red-600"></span> pengajuan sekaligus</p>
                    </div>
                </div>
                
                <form action="{{ route('pinjaman.massReject') }}" method="POST" id="massRejectForm">
                    @csrf
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="pinjaman_ids[]" :value="id">
                    </template>
                    
                    <div class="mt-4">
                        <label class="block text-[13px] font-bold text-stone-700 mb-2">Alasan Penolakan Bersama <span class="text-red-500">*</span></label>
                        <textarea name="alasan_penolakan" rows="3" required class="w-full px-4 py-3 rounded-xl border border-stone-300 bg-stone-50 focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 text-sm outline-none transition-colors" placeholder="Tuliskan kenapa pengajuan-pengajuan ini ditolak..."></textarea>
                    </div>
                </form>
            </div>
            <div class="bg-stone-50 px-6 py-4 border-t border-stone-200 flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                <button type="button" @click="showRejectModal = false" class="px-5 py-2.5 rounded-xl border border-stone-300 bg-white text-stone-700 font-bold text-sm hover:bg-stone-50 transition-colors">Batal</button>
                <button type="submit" form="massRejectForm" class="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-sm shadow-md transition-colors">Tolak Semua Terpilih</button>
            </div>
        </div>
    </div>

        </div> {{-- End of table-content-container --}}
    </div> {{-- End of flex flex-col --}}
</div> {{-- End of pinjamanFilter x-data --}}
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('pinjamanFilter', () => ({
        q: new URLSearchParams(location.search).get('q') || '',
        bidang_id: new URLSearchParams(location.search).get('bidang_id') || '',
        periode_id: new URLSearchParams(location.search).get('periode_id') || '',
        status: new URLSearchParams(location.search).get('status') || '',
        indikator: new URLSearchParams(location.search).get('indikator') || '',
        loading: false,
        timeout: null,
        abortController: null,

        init() {
            this.$watch('q', () => this.debouncedFetch());
            this.$watch('bidang_id', () => this.fetchData());
            this.$watch('periode_id', () => this.fetchData());
            this.$watch('status', () => this.fetchData());
            this.$watch('indikator', () => this.fetchData());

            // Listen for Alpine dispatch from pagination links (if applicable)
            window.addEventListener('pinjaman-paginate', (e) => {
                this.fetchData(e.detail.url);
            });
        },

        get activeFiltersCount() {
            let count = 0;
            if (this.bidang_id !== '') count++;
            if (this.periode_id !== '') count++;
            if (this.status !== '') count++;
            if (this.indikator !== '') count++;
            return count;
        },

        debouncedFetch() {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                this.fetchData();
            }, 400); // 400ms debounce
        },

        async fetchData(targetUrl = null) {
            this.loading = true;
            
            if (this.abortController) {
                this.abortController.abort();
            }
            this.abortController = new AbortController();
            
            let url = targetUrl;
            if (!url) {
                const params = new URLSearchParams();
                if (this.q !== '') params.append('q', this.q);
                if (this.bidang_id !== '') params.append('bidang_id', this.bidang_id);
                if (this.periode_id !== '') params.append('periode_id', this.periode_id);
                if (this.status !== '') params.append('status', this.status);
                if (this.indikator !== '') params.append('indikator', this.indikator);
                
                url = `${window.location.pathname}?${params.toString()}`;
            }

            try {
                window.history.pushState({}, '', url);

                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: this.abortController.signal
                });
                
                const html = await response.text();
                
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('table-content-container');
                
                if (newContent) {
                    document.getElementById('table-content-container').innerHTML = newContent.innerHTML;
                }
                
                // If there's a dynamic badge or counter you can update it here
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error('Failed to fetch data', error);
                }
            } finally {
                this.loading = false;
            }
        }
    }));
});
</script>
@endpush
