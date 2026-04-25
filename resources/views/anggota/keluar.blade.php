@extends('layouts.app')

@section('title', 'Proses Keluar Anggota')
@section('subtitle', 'Analisis & validasi pengeluaran anggota dari koperasi')

@section('actions')
<a href="{{ route('anggota.show', $anggota) }}" class="py-2.5 px-4 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-sm font-medium transition-colors">
    ← Kembali ke Profil
</a>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header Anggota --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center text-white text-xl font-bold shadow-lg">
                {{ strtoupper(substr($anggota->nama, 0, 1)) }}
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">{{ $anggota->nama }}</h3>
                <div class="flex items-center gap-3 text-sm text-slate-500">
                    <span>NIP: {{ $anggota->nip }}</span>
                    <span>•</span>
                    <span>{{ $anggota->bidang->nama_bidang ?? '-' }}</span>
                    <span>•</span>
                    <span>Bergabung: {{ $anggota->tanggal_masuk?->translatedFormat('d M Y') ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- ========================= --}}
        {{-- STEP 1: Cek Pinjaman Aktif --}}
        {{-- ========================= --}}
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden {{ $analisis['has_pinjaman_aktif'] ? 'border-red-200' : 'border-emerald-200' }}">
            <div class="px-6 py-4 border-b {{ $analisis['has_pinjaman_aktif'] ? 'bg-red-50 border-red-200' : 'bg-emerald-50 border-emerald-200' }}">
                <h4 class="text-sm font-bold flex items-center gap-2 {{ $analisis['has_pinjaman_aktif'] ? 'text-red-700' : 'text-emerald-700' }}">
                    @if($analisis['has_pinjaman_aktif'])
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    STEP 1: Pinjaman Aktif Terdeteksi
                    @else
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    STEP 1: Tidak Ada Pinjaman Aktif ✓
                    @endif
                </h4>
            </div>
            <div class="p-6">
                @if($analisis['has_pinjaman_aktif'])
                <div class="text-sm text-red-700 mb-4">
                    <p class="font-semibold mb-2">⚠️ Anggota masih memiliki {{ $analisis['pinjaman_aktif']->count() }} pinjaman aktif:</p>
                </div>
                <div class="space-y-2">
                    @foreach($analisis['pinjaman_aktif'] as $p)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-3">
                        <div class="flex justify-between text-sm">
                            <span class="font-semibold text-red-800">{{ $p->no_referensi }}</span>
                            <span class="font-mono font-bold text-red-700">{{ format_rupiah($p->nominal_pinjaman) }}</span>
                        </div>
                        <p class="text-xs text-red-500 mt-1">Status: {{ ucfirst($p->status->value ?? $p->status) }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="bg-red-100 rounded-xl p-4 mt-4 text-xs text-red-800">
                    <strong>Proses keluar tidak dapat dilanjutkan.</strong> Pinjaman aktif harus dilunasi terlebih dahulu sebelum anggota dapat keluar dari koperasi.
                </div>
                @else
                <p class="text-sm text-emerald-600">Anggota tidak memiliki pinjaman aktif yang belum lunas. Proses dapat dilanjutkan ke STEP 2.</p>
                @endif
            </div>
        </div>

        {{-- ========================= --}}
        {{-- STEP 2: Rincian Simpanan    --}}
        {{-- ========================= --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-blue-50 border-b border-blue-200">
                <h4 class="text-sm font-bold text-blue-700 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    STEP 2: Rincian Simpanan yang Akan Dikembalikan
                </h4>
            </div>
            <div class="p-6">
                <div class="space-y-2 mb-4">
                    @foreach($analisis['rincian_simpanan'] as $item)
                    <div class="flex justify-between items-center text-sm border-b border-dashed border-slate-200 pb-2">
                        <div>
                            <span class="font-medium text-slate-700">{{ $item['nama'] }}</span>
                            @if($item['sudah_ditarik'] > 0)
                            <p class="text-[10px] text-slate-400">Bruto: {{ format_rupiah($item['bruto']) }} − Penarikan: {{ format_rupiah($item['sudah_ditarik']) }}</p>
                            @endif
                        </div>
                        <span class="font-mono font-bold text-slate-800">{{ format_rupiah($item['neto']) }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="bg-slate-100 rounded-xl p-4 flex justify-between items-center">
                    <span class="text-sm font-bold text-slate-700 uppercase">Total Dikembalikan</span>
                    <span class="text-xl font-black text-slate-900 font-mono">{{ format_rupiah($analisis['total_dikembalikan']) }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ========================= --}}
    {{-- STEP 3: Cek Saldo Kas      --}}
    {{-- ========================= --}}
    @if(!$analisis['has_pinjaman_aktif'])
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden {{ $analisis['saldo_cukup'] ? 'border-emerald-200' : 'border-amber-200' }}">
        <div class="px-6 py-4 border-b {{ $analisis['saldo_cukup'] ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-200' }}">
            <h4 class="text-sm font-bold flex items-center gap-2 {{ $analisis['saldo_cukup'] ? 'text-emerald-700' : 'text-amber-700' }}">
                @if($analisis['saldo_cukup'])
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                STEP 3: Saldo Kas Koperasi Mencukupi ✓
                @else
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                STEP 3: Saldo Kas Koperasi Tidak Mencukupi
                @endif
            </h4>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="bg-slate-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-slate-500 uppercase tracking-wider mb-1">Saldo Kas Tersedia</p>
                    <p class="text-lg font-bold font-mono text-slate-800">{{ format_rupiah($analisis['saldo_kas']) }}</p>
                </div>
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <p class="text-xs text-blue-500 uppercase tracking-wider mb-1">Perlu Dikembalikan</p>
                    <p class="text-lg font-bold font-mono text-blue-700">{{ format_rupiah($analisis['total_dikembalikan']) }}</p>
                </div>
                <div class="rounded-xl p-4 text-center {{ $analisis['saldo_cukup'] ? 'bg-emerald-50' : 'bg-red-50' }}">
                    <p class="text-xs uppercase tracking-wider mb-1 {{ $analisis['saldo_cukup'] ? 'text-emerald-500' : 'text-red-500' }}">
                        {{ $analisis['saldo_cukup'] ? 'Sisa Setelah Proses' : 'Defisit / Kekurangan' }}
                    </p>
                    <p class="text-lg font-bold font-mono {{ $analisis['saldo_cukup'] ? 'text-emerald-700' : 'text-red-700' }}">
                        {{ $analisis['saldo_cukup'] ? format_rupiah($analisis['saldo_kas'] - $analisis['total_dikembalikan']) : format_rupiah($analisis['defisit']) }}
                    </p>
                </div>
            </div>

            @if(!$analisis['saldo_cukup'])
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <p class="text-sm text-amber-800 font-semibold mb-2">⚠️ Kas koperasi tidak mencukupi untuk mengembalikan seluruh simpanan anggota.</p>
                <p class="text-xs text-amber-700 mb-3">Kekurangan sebesar <strong>{{ format_rupiah($analisis['defisit']) }}</strong>. Proses keluar tidak dapat dilanjutkan saat ini.</p>
                <a href="{{ route('keuangan.simulasi') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Buka Simulasi Proyeksi Keuangan
                </a>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ========================= --}}
    {{-- STEP 4: Konfirmasi & Proses --}}
    {{-- ========================= --}}
    @if($analisis['bisa_proses'])
    <div class="bg-white rounded-2xl border border-red-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-red-50 border-b border-red-200">
            <h4 class="text-sm font-bold text-red-700 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                STEP 4: Konfirmasi Pengeluaran Anggota
            </h4>
        </div>
        <div class="p-6">
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 text-sm text-red-700 space-y-2">
                <p class="font-bold">⚠️ Peringatan: Tindakan ini tidak dapat dibatalkan!</p>
                <ul class="list-disc pl-4 text-xs space-y-1">
                    <li>Seluruh simpanan anggota ({{ format_rupiah($analisis['total_dikembalikan']) }}) akan dicatat sebagai <strong>penarikan simpanan</strong> dan mengurangi saldo kas koperasi.</li>
                    <li>Status anggota akan diubah menjadi <strong>Nonaktif / Keluar</strong>.</li>
                    <li>Arsip keluar akan disimpan. Jika anggota ingin mendaftar ulang di kemudian hari, wajib menyetor kembali sebesar <strong>{{ format_rupiah($analisis['total_dikembalikan']) }}</strong>.</li>
                    <li>Neraca dan laporan keuangan akan otomatis menyesuaikan.</li>
                </ul>
            </div>

            <form action="{{ route('anggota.keluar.proses', $anggota) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin memproses keluarnya anggota {{ $anggota->nama }} dari koperasi?')">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Catatan (Opsional)</label>
                        <textarea name="catatan" rows="3" class="w-full px-4 py-3 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all" placeholder="Alasan keluar, catatan khusus, dll..."></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="konfirmasi" id="konfirmasi" value="1" required class="w-4 h-4 rounded border-red-300 text-red-600 focus:ring-red-500">
                        <label for="konfirmasi" class="text-sm text-red-700 font-medium">
                            Saya memahami konsekuensi dan mengkonfirmasi proses pengeluaran anggota <strong>{{ $anggota->nama }}</strong>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-sm transition-colors shadow-lg shadow-red-600/20">
                        Proses Keluarkan Anggota & Kembalikan Simpanan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Arsip Keluar Sebelumnya --}}
    @if($analisis['arsip_sebelumnya'])
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
        <h4 class="text-sm font-bold text-amber-700 mb-3">📋 Riwayat Keluar Sebelumnya</h4>
        <div class="bg-white rounded-xl p-4 border border-amber-100">
            <div class="flex justify-between items-center text-sm mb-2">
                <span class="text-slate-600">Tanggal keluar terakhir</span>
                <span class="font-semibold text-slate-800">{{ $analisis['arsip_sebelumnya']->tanggal_keluar->translatedFormat('d F Y') }}</span>
            </div>
            <div class="flex justify-between items-center text-sm mb-2">
                <span class="text-slate-600">Total simpanan dikembalikan</span>
                <span class="font-mono font-bold text-slate-800">{{ format_rupiah($analisis['arsip_sebelumnya']->total_simpanan_dikembalikan) }}</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-slate-600">Wajib setor jika daftar ulang</span>
                <span class="font-mono font-bold text-amber-700">{{ format_rupiah($analisis['arsip_sebelumnya']->nominal_wajib_setor_ulang) }}</span>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
