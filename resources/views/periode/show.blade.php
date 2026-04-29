@extends('layouts.app')

@section('title', $periode->nama_periode)
@section('subtitle', 'Detail periode & link pengajuan')

@section('actions')
<a href="{{ route('periode.index') }}" class="text-sm text-slate-500 hover:text-slate-700 transition-colors">← Kembali</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Kolom Kiri: Info & Link --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Link Share Card --}}
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-6 text-white shadow-lg shadow-blue-600/15">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="font-semibold text-lg">Link Pengajuan Anggota</h3>
                    <p class="text-blue-200 text-sm mt-0.5">Bagikan link ini ke anggota untuk mengajukan pinjaman</p>
                </div>
                <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
                    @if($periode->statusEfektif() === \App\Enums\StatusPeriode::Buka) bg-emerald-400/20 text-emerald-200
                    @elseif($periode->statusEfektif() === \App\Enums\StatusPeriode::Terjadwal) bg-amber-400/20 text-amber-200
                    @else bg-red-400/20 text-red-200 @endif">
                    {{ $periode->statusEfektif()->label() }}
                </span>
            </div>

            <div class="bg-black/20 rounded-xl p-4 flex items-center gap-3 mb-4">
                <input type="text" id="link-pengajuan" value="{{ $periode->link_pengajuan }}" readonly
                       class="flex-1 bg-transparent text-white text-sm font-mono border-none focus:outline-none truncate">
                <button type="button" onclick="copyLink()" id="btn-copy"
                        class="shrink-0 px-3 py-1.5 rounded-lg bg-white/10 text-white text-xs font-medium hover:bg-white/20 transition-colors">
                    Salin
                </button>
            </div>

            <div class="flex flex-wrap gap-2">
                {{-- Edit Periode --}}
                <a href="{{ route('periode.edit', $periode) }}"
                   class="px-3 py-1.5 rounded-lg bg-white/10 text-white text-xs font-medium hover:bg-white/20 transition-colors inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Periode
                </a>

                {{-- Tutup/Buka Periode --}}
                @if($periode->isBuka())
                <form method="POST" action="{{ route('periode.tutup', $periode) }}" class="inline"
                      onsubmit="return confirm('Yakin ingin menutup periode ini? Anggota tidak bisa mengajukan pinjaman lagi via link ini.')">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-200 text-xs font-medium hover:bg-red-500/30 transition-colors">
                        Tutup Periode
                    </button>
                </form>
                @else
                <form method="POST" action="{{ route('periode.buka', $periode) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500/20 text-emerald-200 text-xs font-medium hover:bg-emerald-500/30 transition-colors">
                        Buka Kembali
                    </button>
                </form>
                @endif

                {{-- Reset Token --}}
                <form method="POST" action="{{ route('periode.reset-token', $periode) }}" class="inline"
                      onsubmit="return confirm('Yakin? Link lama tidak akan berfungsi lagi.')">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-amber-500/20 text-amber-200 text-xs font-medium hover:bg-amber-500/30 transition-colors">
                        Reset Link
                    </button>
                </form>

                {{-- Hapus Periode (hanya jika belum ada pengajuan) --}}
                @if($periode->pinjaman->isEmpty())
                <form method="POST" action="{{ route('periode.destroy', $periode) }}" class="inline"
                      onsubmit="return confirm('Yakin ingin menghapus periode ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-600/30 text-red-200 text-xs font-medium hover:bg-red-600/50 transition-colors inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Periode
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Daftar Pengajuan di Periode Ini --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800">Pengajuan Masuk</h3>
                <p class="text-sm text-slate-500 mt-0.5">{{ $periode->pinjaman->count() }} pengajuan</p>
            </div>

            @if($periode->pinjaman->isEmpty())
            <div class="p-10 text-center">
                <p class="text-slate-400 text-sm">Belum ada pengajuan di periode ini.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100">
                            <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">No. Ref</th>
                            <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Anggota</th>
                            <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Nominal</th>
                            <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Tenor</th>
                            <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                            <th class="px-5 py-3 text-xs font-semibold text-slate-500 uppercase">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($periode->pinjaman as $pinjaman)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3 font-medium text-blue-600 whitespace-nowrap">{{ $pinjaman->no_referensi }}</td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <p class="font-medium text-slate-800">{{ $pinjaman->anggota->nama }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $pinjaman->anggota->nip }}</p>
                            </td>
                            <td class="px-5 py-3 text-slate-700 font-mono whitespace-nowrap">{{ format_rupiah($pinjaman->nominal_pinjaman) }}</td>
                            <td class="px-5 py-3 text-slate-600 whitespace-nowrap">{{ $pinjaman->tenor_bulan }} bln</td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium
                                    bg-{{ $pinjaman->status->color() }}-50 text-{{ $pinjaman->status->color() }}-700
                                    ring-1 ring-inset ring-{{ $pinjaman->status->color() }}-600/20">
                                    {{ $pinjaman->status->label() }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500 whitespace-nowrap">{{ $pinjaman->tanggal_pengajuan->format('d/m/Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Kolom Kanan: Detail Info --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <h3 class="font-semibold text-slate-800 mb-4 text-sm uppercase tracking-wider">Informasi Periode</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Tahun</p>
                    <p class="text-sm font-medium text-slate-800">{{ $periode->tahun }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Tanggal Buka</p>
                    <p class="text-sm font-medium text-slate-800">{{ $periode->tanggal_buka->format('d F Y') }}</p>
                </div>
                @if($periode->tanggal_tutup)
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Tanggal Tutup</p>
                    <p class="text-sm font-medium text-slate-800">{{ $periode->tanggal_tutup->format('d F Y') }}</p>
                </div>
                @endif
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Batas Pelunasan</p>
                    <p class="text-sm font-medium text-slate-800">Bulan ke-{{ $periode->batas_bulan_pelunasan }} ({{ nama_bulan($periode->batas_bulan_pelunasan) }})</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Limit Per Anggota</p>
                    <p class="text-sm font-bold text-slate-800">{{ format_rupiah($periode->limit_per_anggota) }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Dibuka Oleh</p>
                    <p class="text-sm font-medium text-slate-800">{{ $periode->pembuka->nama ?? '-' }}</p>
                </div>
                @if($periode->catatan)
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Catatan</p>
                    <p class="text-sm text-slate-600">{{ $periode->catatan }}</p>
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
        btn.textContent = '✓ Tersalin!';
        btn.classList.add('bg-emerald-500/30');
        setTimeout(() => {
            btn.textContent = 'Salin';
            btn.classList.remove('bg-emerald-500/30');
        }, 2000);
    });
}
</script>
@endsection
