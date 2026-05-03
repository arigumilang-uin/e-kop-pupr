@extends('layouts.app')

@section('title', $periode->nama_periode)
@section('subtitle', 'Detail periode & link pengajuan pinjaman anggota')

@section('actions')
<a href="{{ route('periode.index') }}" class="py-2.5 px-4 rounded-xl bg-white border border-stone-200 hover:bg-stone-50 text-stone-700 text-sm font-bold transition-colors flex items-center gap-2 shadow-sm">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Kembali
</a>
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
                <a href="{{ route('periode.edit', $periode) }}"
                   class="px-4 py-2 rounded-xl bg-white/10 text-white text-xs font-bold hover:bg-white/20 transition-colors inline-flex items-center gap-1.5 border border-white/5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Periode
                </a>

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

                {{-- Hapus Periode (hanya jika belum ada pengajuan) --}}
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
                    <p class="text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-1">Maks. Batas Pelunasan</p>
                    <p class="text-sm font-bold text-stone-800">Bulan ke-{{ $periode->batas_bulan_pelunasan }} ({{ nama_bulan($periode->batas_bulan_pelunasan) }})</p>
                </div>
                <div class="h-px bg-stone-100"></div>
                <div class="bg-[#043d2e]/5 border border-[#043d2e]/10 -mx-3 p-3 rounded-xl border-l-[3px] border-l-[#043d2e]">
                    <p class="text-[11px] font-bold text-[#043d2e]/60 uppercase tracking-widest mb-1">Limit Pinjaman Plafon</p>
                    <p class="text-base font-black font-mono text-[#043d2e]">{{ format_rupiah($periode->limit_per_anggota) }}</p>
                </div>
                <div class="h-px bg-stone-100"></div>
                <div>
                    <p class="text-[11px] font-bold text-stone-500 uppercase tracking-widest mb-1">Angsuran Bulan Berjalan</p>
                    @if($periode->angsuran_bulan_berjalan)
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Aktif — Tenor +1 Bulan
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-bold bg-stone-100 text-stone-500 border border-stone-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        Tidak Aktif — Standar
                    </span>
                    @endif
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
