@extends('layouts.app')

@section('title', 'Manajemen Persetujuan Pinjaman')
@section('subtitle', 'Daftar pengajuan pinjaman anggota menunggu validasi kas dan approval')

@section('content')

{{-- Statistik Persetujuan --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 border border-slate-200 flex items-center justify-between shadow-sm">
        <div>
            <p class="text-sm font-medium text-slate-500 mb-1">Total Kas Tersedia</p>
            <h3 class="text-2xl font-bold font-mono text-blue-700">Rp {{ number_format($totalSaldoKoperasi, 0, ',', '.') }}</h3>
        </div>
        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    {{-- Tombol Copy Link Cek Status --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">
        <p class="text-sm font-medium text-slate-500 mb-3">Link Cek Status Pinjaman</p>
        <div class="flex items-center gap-2">
            <input type="text" id="linkCekStatus" value="{{ route('pinjaman.guest.status') }}" readonly
                   class="flex-1 px-3 py-2 rounded-lg bg-slate-50 border border-slate-200 text-xs font-mono text-slate-600 truncate">
            <button onclick="navigator.clipboard.writeText(document.getElementById('linkCekStatus').value); this.textContent='Tersalin!'; setTimeout(() => this.textContent='Copy', 2000);"
                    class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition-colors whitespace-nowrap">
                Copy
            </button>
        </div>
        <p class="text-[11px] text-slate-400 mt-2">Bagikan link ini kepada anggota agar mereka bisa cek status pinjaman.</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-200 text-[13px] uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-4">Referensi / Tgl</th>
                    <th scope="col" class="px-6 py-4">Peminjam</th>
                    <th scope="col" class="px-6 py-4">Nominal / Tenor</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($pinjamans as $pinjaman)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-mono text-blue-600 font-semibold">{{ $pinjaman->no_referensi }}</span>
                            <span class="text-xs text-slate-500 mt-0.5">{{ $pinjaman->tanggal_pengajuan->format('d M Y - H:i') }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-slate-800 font-semibold">{{ $pinjaman->anggota->nama }}</span>
                            <span class="text-xs text-slate-500 mt-0.5">NIP: {{ $pinjaman->anggota->nip }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-800 font-mono">Rp {{ number_format($pinjaman->nominal_pinjaman, 0, ',', '.') }}</span>
                            <span class="text-xs text-slate-500 mt-0.5">{{ $pinjaman->tenor_bulan }} Bulan @ {{ number_format($pinjaman->bunga_persen, 1) }}% Flat</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($pinjaman->status->value === 'menunggu')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 border border-amber-200 text-xs font-semibold">
                            Menunggu Approval
                        </span>
                        @elseif($pinjaman->status->value === 'berjalan')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">
                            Aktif / Berjalan
                        </span>
                        @elseif($pinjaman->status->value === 'ditolak')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-red-50 text-red-700 border border-red-200 text-xs font-semibold">
                            Ditolak
                        </span>
                        @elseif($pinjaman->status->value === 'dibatalkan')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 border border-slate-300 text-xs font-semibold">
                            Dibatalkan
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 border border-slate-300 text-xs font-semibold capitalize">
                            {{ $pinjaman->status->label() }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('pinjaman.show', $pinjaman->id) }}" 
                           class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors border border-blue-200 hover:border-blue-600 text-[13px]">
                            Tinjau
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                        Belum ada riwayat pengajuan pinjaman terbaru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($pinjamans->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $pinjamans->links() }}
    </div>
    @endif
</div>
@endsection
