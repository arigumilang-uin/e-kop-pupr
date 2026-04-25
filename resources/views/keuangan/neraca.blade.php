@extends('layouts.app')

@section('title', 'Neraca Keuangan')
@section('subtitle', 'Laporan Posisi Keuangan Koperasi Simpan Pinjam PUPR Riau')

@section('actions')
<a href="{{ route('keuangan.laporan') }}" class="py-2.5 px-4 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-900 text-sm font-medium transition-colors">
    ← Laporan Keuangan
</a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header Formal --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-8 py-6 text-center">
            <p class="text-slate-400 text-xs font-medium uppercase tracking-[.3em] mb-1">Koperasi Simpan Pinjam</p>
            <h1 class="text-xl font-bold text-white tracking-wide">KOPERASI PEGAWAI PUPR PROVINSI RIAU</h1>
            <div class="w-24 h-0.5 bg-amber-400 mx-auto mt-3 mb-3 rounded-full"></div>
            <h2 class="text-lg font-bold text-white uppercase tracking-wider">NERACA</h2>
            <p class="text-slate-300 text-sm mt-1">Per {{ \Carbon\Carbon::parse($neraca['tanggal'])->translatedFormat('d F Y') }}</p>
        </div>

        <div class="p-6 lg:p-8">
            {{-- ============================================================ --}}
            {{-- KOLOM DUA: AKTIVA (Kiri) | PASIVA (Kanan)                    --}}
            {{-- ============================================================ --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- ======================== --}}
                {{--  AKTIVA (ASET)           --}}
                {{-- ======================== --}}
                <div>
                    <div class="border-b-2 border-slate-800 pb-2 mb-5">
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">A K T I V A</h3>
                    </div>

                    <div class="mb-6">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 pl-1">Aktiva Lancar</p>

                        {{-- Kas --}}
                        <div class="flex justify-between items-baseline py-1.5 border-b border-dashed border-slate-200">
                            <span class="text-sm text-slate-700 pl-4">Kas & Setara Kas</span>
                            <span class="text-sm font-mono font-semibold text-slate-800">{{ format_rupiah($neraca['aktiva']['kas']) }}</span>
                        </div>

                        {{-- Piutang --}}
                        <div class="flex justify-between items-baseline py-1.5 border-b border-dashed border-slate-200">
                            <div class="pl-4">
                                <span class="text-sm text-slate-700">Piutang Pokok Pinjaman Anggota</span>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    Total Pokok: {{ format_rupiah($neraca['aktiva']['piutang_detail']['total_pokok']) }}
                                    − Terbayar: {{ format_rupiah($neraca['aktiva']['piutang_detail']['pokok_terbayar']) }}
                                </p>
                            </div>
                            <span class="text-sm font-mono font-semibold text-slate-800 whitespace-nowrap">{{ format_rupiah($neraca['aktiva']['piutang_pinjaman']) }}</span>
                        </div>
                    </div>

                    {{-- Total Aktiva --}}
                    <div class="border-t-2 border-b-2 border-slate-800 py-3 flex justify-between items-baseline bg-slate-50 px-4 rounded-lg">
                        <span class="text-sm font-black text-slate-800 uppercase tracking-wide">TOTAL AKTIVA</span>
                        <span class="text-base font-mono font-black text-slate-900">{{ format_rupiah($neraca['aktiva']['total']) }}</span>
                    </div>
                </div>

                {{-- ======================== --}}
                {{--  PASIVA                  --}}
                {{-- ======================== --}}
                <div>
                    <div class="border-b-2 border-slate-800 pb-2 mb-5">
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">P A S I V A</h3>
                    </div>

                    {{-- ======================== --}}
                    {{-- I. KEWAJIBAN (HUTANG)    --}}
                    {{-- ======================== --}}
                    <div class="mb-6">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 pl-1">I. Kewajiban (Hutang)</p>

                        @foreach($neraca['kewajiban']['simpanan_items'] as $item)
                        <div class="flex justify-between items-baseline py-1 border-b border-dashed border-slate-200">
                            <span class="text-sm text-slate-700 pl-4">{{ $item->nama }}</span>
                            <span class="text-sm font-mono text-slate-700">{{ format_rupiah($item->total) }}</span>
                        </div>
                        @endforeach

                        <div class="flex justify-between items-baseline py-1 border-b border-dashed border-slate-200">
                            <span class="text-sm text-slate-700 pl-4">Cadangan Dana Resiko</span>
                            <span class="text-sm font-mono text-slate-700">{{ format_rupiah($neraca['kewajiban']['dana_resiko']) }}</span>
                        </div>


                        <div class="flex justify-between items-baseline py-2 border-t border-slate-300 mt-2">
                            <span class="text-sm font-bold text-slate-700 pl-4">Total Kewajiban</span>
                            <span class="text-sm font-mono font-bold text-slate-800">{{ format_rupiah($neraca['kewajiban']['total']) }}</span>
                        </div>
                    </div>

                    {{-- ======================== --}}
                    {{-- II. MODAL / EKUITAS      --}}
                    {{-- ======================== --}}
                    <div class="mb-6">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3 pl-1">II. Modal / Ekuitas</p>

                        @foreach($neraca['modal']['simpanan_items'] as $item)
                        <div class="flex justify-between items-baseline py-1 border-b border-dashed border-slate-200">
                            <span class="text-sm text-slate-700 pl-4">{{ $item->nama }}</span>
                            <span class="text-sm font-mono text-slate-700">{{ format_rupiah($item->total) }}</span>
                        </div>
                        @endforeach

                        {{-- SHU --}}
                        <div class="flex justify-between items-baseline py-1 border-b border-dashed border-slate-200">
                            <span class="text-sm text-slate-700 pl-4">SHU Tahun Berjalan</span>
                            <span class="text-sm font-mono font-semibold {{ $neraca['modal']['shu_berjalan'] >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                                {{ format_rupiah($neraca['modal']['shu_berjalan']) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-baseline py-2 border-t border-slate-300 mt-2">
                            <span class="text-sm font-bold text-slate-700 pl-4">Total Modal / Ekuitas</span>
                            <span class="text-sm font-mono font-bold text-slate-800">{{ format_rupiah($neraca['modal']['total']) }}</span>
                        </div>
                    </div>

                    {{-- Total Pasiva --}}
                    <div class="border-t-2 border-b-2 border-slate-800 py-3 flex justify-between items-baseline bg-slate-50 px-4 rounded-lg">
                        <span class="text-sm font-black text-slate-800 uppercase tracking-wide">TOTAL PASIVA</span>
                        <span class="text-base font-mono font-black text-slate-900">{{ format_rupiah($neraca['pasiva']['total']) }}</span>
                    </div>
                </div>

            </div>
        </div>

        {{-- Balance Check Footer --}}
        <div class="border-t border-slate-200 px-8 py-4 {{ $neraca['is_balance'] ? 'bg-emerald-50' : 'bg-red-50' }}">
            <div class="flex flex-col md:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    @if($neraca['is_balance'])
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center border border-emerald-200">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-emerald-800">NERACA SEIMBANG (BALANCED)</p>
                        <p class="text-xs text-emerald-600">Total Aktiva = Total Pasiva — Tidak ada selisih</p>
                    </div>
                    @else
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center border border-red-200">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-red-800">NERACA TIDAK SEIMBANG</p>
                        <p class="text-xs text-red-600">Selisih: {{ format_rupiah($neraca['selisih']) }} — Perlu investigasi lebih lanjut</p>
                    </div>
                    @endif
                </div>

                <div class="flex items-center gap-6 text-sm font-mono">
                    <div class="text-center">
                        <p class="text-[10px] text-slate-500 font-sans uppercase tracking-wider mb-0.5">AKTIVA</p>
                        <p class="font-bold text-slate-800">{{ format_rupiah($neraca['aktiva']['total']) }}</p>
                    </div>
                    <span class="text-slate-400 text-lg">=</span>
                    <div class="text-center">
                        <p class="text-[10px] text-slate-500 font-sans uppercase tracking-wider mb-0.5">PASIVA</p>
                        <p class="font-bold text-slate-800">{{ format_rupiah($neraca['pasiva']['total']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Catatan Kaki --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Catatan atas Neraca</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-slate-500 leading-relaxed">
            <div class="space-y-2">
                <p><strong class="text-slate-700">1. Kas & Setara Kas</strong> — Saldo liquid yang tersedia di kas koperasi (seluruh dana masuk dikurangi seluruh dana keluar).</p>
                <p><strong class="text-slate-700">2. Piutang Pokok</strong> — Sisa pokok pinjaman yang berstatus "berjalan" dan belum diangsur oleh anggota (bunga belum diakui sebagai aset).</p>
                <p><strong class="text-slate-700">3. Simpanan Sukarela</strong> — Dicatat sebagai kewajiban karena secara prinsip dapat ditarik oleh anggota.</p>
            </div>
            <div class="space-y-2">
                <p><strong class="text-slate-700">4. Cadangan Dana Resiko</strong> — Potongan 1.5% dari setiap pencairan pinjaman, dicadangkan untuk menutup risiko kredit macet.</p>
                <p><strong class="text-slate-700">5. Simpanan Pokok, Wajib & SWP</strong> — Dicatat sebagai modal karena bersifat tetap dan tidak dapat ditarik selama anggota masih aktif di koperasi.</p>
                <p><strong class="text-slate-700">6. SHU Tahun Berjalan</strong> — Selisih antara total pendapatan (bunga pinjaman + biaya admin) dengan total beban operasional koperasi.</p>
            </div>
        </div>
    </div>

    {{-- Tanda Tangan --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center text-sm text-slate-700">
            <div>
                <p class="font-medium">Disusun oleh,</p>
                <div class="h-20"></div>
                <div class="border-t border-slate-300 pt-2 mx-auto w-48">
                    <p class="font-semibold">Bendahara</p>
                    <p class="text-xs text-slate-400">Koperasi PUPR Riau</p>
                </div>
            </div>
            <div>
                <p class="font-medium">Diperiksa oleh,</p>
                <div class="h-20"></div>
                <div class="border-t border-slate-300 pt-2 mx-auto w-48">
                    <p class="font-semibold">Pengawas</p>
                    <p class="text-xs text-slate-400">Koperasi PUPR Riau</p>
                </div>
            </div>
            <div>
                <p class="font-medium">Disetujui oleh,</p>
                <div class="h-20"></div>
                <div class="border-t border-slate-300 pt-2 mx-auto w-48">
                    <p class="font-semibold">Ketua Koperasi</p>
                    <p class="text-xs text-slate-400">Koperasi PUPR Riau</p>
                </div>
            </div>
        </div>
        <p class="text-center text-[10px] text-slate-400 mt-6">Pekanbaru, {{ now()->translatedFormat('d F Y') }}</p>
    </div>

</div>
@endsection
