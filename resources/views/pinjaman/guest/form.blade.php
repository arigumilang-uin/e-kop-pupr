@extends('layouts.guest')

@section('title', 'Pengajuan Pinjaman')

@section('content')
<div class="w-full max-w-4xl py-6">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-white">Form Pengajuan Pinjaman</h1>
        <p class="text-slate-400 text-sm mt-1">Periode: {{ $periode->nama_periode }}</p>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <h3 class="text-red-400 font-medium text-sm mb-1">Gagal mengirim pengajuan</h3>
                <ul class="text-red-300 text-sm list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white/5 backdrop-blur-xl rounded-2xl border border-white/10 shadow-2xl overflow-hidden">
        <form method="POST" action="{{ route('pinjaman.guest.submit', $periode->token) }}" class="divide-y divide-white/10">
            @csrf

            {{-- Tahap 1: Data Anggota --}}
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-sm">1</div>
                    <h2 class="text-lg font-semibold text-white">Data Keanggotaan</h2>
                </div>

                <div class="max-w-md">
                    <div>
                        <label for="nip" class="block text-sm font-medium text-slate-300 mb-1.5">NIP <span class="text-red-400">*</span></label>
                        <input type="text" id="nip" name="nip" value="{{ old('nip') }}" required placeholder="Masukkan NIP Anda"
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border border-white/10 text-white placeholder-slate-600
                                      focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 text-sm">
                        <p class="text-xs text-slate-500 mt-2">Sistem akan otomatis mencari data keanggotaan berdasarkan NIP.</p>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('pinjaman.guest.status') }}" class="text-xs text-blue-400 hover:text-blue-300 transition-colors underline underline-offset-2">
                        Sudah pernah mengajukan? Cek status pengajuan di sini →
                    </a>
                </div>
            </div>

            {{-- Tahap 2: Data Bank --}}
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-sm">2</div>
                    <h2 class="text-lg font-semibold text-white">Data Rekening Bank</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="nama_bank" class="block text-sm font-medium text-slate-300 mb-1.5">Nama Bank <span class="text-red-400">*</span></label>
                        <input type="text" id="nama_bank" name="nama_bank" value="{{ old('nama_bank') }}" required placeholder="Contoh: Bank Riau Kepri / BNI"
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border border-white/10 text-white placeholder-slate-600
                                      focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 text-sm">
                    </div>
                    <div>
                        <label for="no_rekening" class="block text-sm font-medium text-slate-300 mb-1.5">Nomor Rekening <span class="text-red-400">*</span></label>
                        <input type="text" id="no_rekening" name="no_rekening" value="{{ old('no_rekening') }}" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border border-white/10 text-white placeholder-slate-600
                                      focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 text-sm leading-none font-mono">
                    </div>
                    <div>
                        <label for="nama_rekening" class="block text-sm font-medium text-slate-300 mb-1.5">Nama Pemilik Rekening <span class="text-red-400">*</span></label>
                        <input type="text" id="nama_rekening" name="nama_rekening" value="{{ old('nama_rekening') }}" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border border-white/10 text-white placeholder-slate-600
                                      focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 text-sm">
                        <p class="text-xs text-slate-500 mt-2">Sebaiknya sama dengan nama peminjam</p>
                    </div>
                </div>
            </div>

            {{-- Tahap 3: Pengajuan Pinjaman --}}
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-sm">3</div>
                    <h2 class="text-lg font-semibold text-white">Detail Pengajuan & Simulasi</h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Form Input Simulasi --}}
                    <div class="space-y-6">
                        {{-- Nominal --}}
                        <div>
                            <label for="nominal_pinjaman" class="block text-sm font-medium text-slate-300 mb-1.5">Nominal Pengajuan (Rp) <span class="text-red-400">*</span></label>
                            <input type="number" id="nominal_pinjaman" name="nominal_pinjaman" value="{{ old('nominal_pinjaman', 5000000) }}" required min="100000" max="{{ $pengaturan['limit'] }}" step="100000"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-900/50 border border-white/10 text-white placeholder-slate-600
                                          focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 text-2xl font-bold font-mono">
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs text-slate-500">Min 100.000</span>
                                <span class="text-xs text-blue-400">Limit: Rp {{ number_format($pengaturan['limit'], 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- Tenor (Slider) --}}
                        <div>
                            <label for="tenor_bulan" class="block text-sm font-medium text-slate-300 mb-2">Tenor Pinjaman: <span id="tenor-display" class="text-blue-400 font-bold text-lg ml-1">...</span></label>
                            {{-- Input Range (Toggle) --}}
                            <input type="range" id="tenor_bulan" name="tenor_bulan" required
                                   min="{{ $pengaturan['tenor_min'] }}" max="{{ $pengaturan['tenor_maks'] }}" value="{{ old('tenor_bulan', min(12, $pengaturan['tenor_maks'])) }}"
                                   class="w-full h-2.5 bg-slate-700/50 rounded-lg appearance-none cursor-pointer accent-blue-500">
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs text-slate-500">{{ $pengaturan['tenor_min'] }} Bulan</span>
                                <span class="text-xs text-slate-500">Maks. Bulan ke-{{ $pengaturan['batas_bulan'] }} ({{ $pengaturan['tenor_maks'] }} bln)</span>
                            </div>
                        </div>
                    </div>

                    {{-- Hasil Simulasi Real-time --}}
                    <div class="bg-slate-900/50 rounded-2xl p-5 border border-white/5 space-y-4">
                        <div class="text-center pb-4 border-b border-white/5">
                            <p class="text-slate-400 text-sm mb-1">Total Angsuran / Bulan</p>
                            <p id="res-angsuran" class="text-3xl font-bold text-white font-mono">Rp 0</p>
                            <div class="flex justify-center gap-4 mt-2 text-xs">
                                <span class="text-slate-500">Pokok: <span id="res-pokok" class="text-slate-300">Rp 0</span></span>
                                <span class="text-slate-500">Bunga flat: <span id="res-bunga" class="text-slate-300">Rp 0</span></span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <p class="text-xs text-amber-500/80 mb-2 mt-2 uppercase tracking-wider font-semibold">Pemotongan di Muka (Total {{ $pengaturan['swp_persen'] + $pengaturan['resiko_persen'] + $pengaturan['admin_persen'] }}%)</p>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-400">Total Potongan (SWP+Resiko+Admin)</span>
                                <span id="res-potongan" class="text-amber-400 font-mono">- Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center p-3 mt-3 bg-emerald-500/10 rounded-xl border border-emerald-500/20">
                                <span class="text-emerald-400 font-semibold text-sm">Dana Bersih Diterima</span>
                                <span id="res-diterima" class="text-emerald-400 font-bold font-mono text-lg">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(session('needs_override_confirmation'))
            <div class="p-8 bg-amber-500/10 border-t border-b border-amber-500/20">
                <div class="flex gap-4">
                    <div class="shrink-0 mt-1">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-amber-500 font-bold mb-1">Konfirmasi Pengajuan Tambahan</h3>
                        <p class="text-amber-200/80 text-sm mb-4">
                            {{ session('needs_override_confirmation') }}
                        </p>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="confirm_override" value="1" required
                                   class="w-5 h-5 rounded border-amber-500/50 bg-amber-500/10 text-amber-500 focus:ring-amber-500 focus:ring-offset-slate-900 focus:ring-offset-2">
                            <span class="text-sm font-medium text-amber-100 group-hover:text-amber-300 transition-colors">Saya Yakin Melanjutkan Pengajuan Ini</span>
                        </label>
                    </div>
                </div>
            </div>
            @endif

            {{-- Submit --}}
            <div class="p-8 bg-black/20 flex flex-col md:flex-row gap-6 items-center justify-between">
                <p class="text-sm text-slate-400 max-w-xl">
                    Dengan klik "Kirim Pengajuan", saya menyatakan bahwa data di atas benar dan bersedia mematuhi aturan potongan {{ $pengaturan['swp_persen'] + $pengaturan['resiko_persen'] + $pengaturan['admin_persen'] }}% serta bunga flat sebesar {{ $pengaturan['bunga_persen'] }}%.
                </p>
                <button type="submit"
                        class="shrink-0 py-3 px-8 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 text-white font-medium text-sm
                               hover:from-blue-500 hover:to-blue-400 transition-all duration-200 shadow-lg shadow-blue-500/25">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // === Konfigurasi dari Server (dirender sekali saat page load) ===
    const CONFIG = {
        bungaPersen: {{ $pengaturan['bunga_persen'] }},
        swpPersen: {{ $pengaturan['swp_persen'] }},
        resikoPersen: {{ $pengaturan['resiko_persen'] }},
        adminPersen: {{ $pengaturan['admin_persen'] }},
    };

    // DOM References
    const elNominal = document.getElementById('nominal_pinjaman');
    const elTenor = document.getElementById('tenor_bulan');
    const elTenorDisplay = document.getElementById('tenor-display');

    // Hasil Simulasi Card
    const elAngsuran = document.getElementById('res-angsuran');
    const elPokok = document.getElementById('res-pokok');
    const elBunga = document.getElementById('res-bunga');
    const elPotongan = document.getElementById('res-potongan');
    const elDiterima = document.getElementById('res-diterima');

    function formatRp(val) {
        return 'Rp ' + Math.round(val).toLocaleString('id-ID');
    }

    function hitungSimulasi() {
        const nominal = parseFloat(elNominal.value) || 0;
        const tenor = parseInt(elTenor.value) || 1;

        elTenorDisplay.textContent = tenor + ' Bulan';

        if (nominal < 100000 || tenor < 1) {
            elAngsuran.textContent = 'Rp 0';
            return;
        }

        // Hitung Bunga & Angsuran
        const totalBunga = nominal * (CONFIG.bungaPersen / 100);
        const angsuranPokok = nominal / tenor;
        const angsuranBunga = totalBunga / tenor;
        const totalAngsuran = angsuranPokok + angsuranBunga;

        // Hitung Potongan 5% di muka
        const potonganSwp = nominal * (CONFIG.swpPersen / 100);
        const potonganResiko = nominal * (CONFIG.resikoPersen / 100);
        const potonganAdmin = nominal * (CONFIG.adminPersen / 100);
        const totalPotongan = potonganSwp + potonganResiko + potonganAdmin;
        
        const danaDiterima = nominal - totalPotongan;

        // Tampilkan Hasil
        elAngsuran.textContent = formatRp(totalAngsuran);
        elPokok.textContent = formatRp(angsuranPokok);
        elBunga.textContent = formatRp(angsuranBunga);
        elPotongan.textContent = '- ' + formatRp(totalPotongan);
        elDiterima.textContent = formatRp(danaDiterima);
    }

    elNominal.addEventListener('input', hitungSimulasi);
    elTenor.addEventListener('input', hitungSimulasi);

    // Initial load
    hitungSimulasi();
</script>
@endsection
