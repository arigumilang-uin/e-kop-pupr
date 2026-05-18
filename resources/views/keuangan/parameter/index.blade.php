@extends('layouts.app')

@section('title', 'Manajemen Parameter Laporan')
@section('subtitle', 'Pengaturan pos, nama, dan nominal komponen laporan keuangan.')

@section('content')
<div class="space-y-6" x-data="{ tab: '{{ $tab }}' }">

    {{-- Filter Tahun & Info --}}
    <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex gap-2 p-1.5 bg-stone-100 rounded-xl border border-stone-200/60 shadow-sm w-full md:w-auto overflow-x-auto">
            <button @click="tab = 'neraca'" 
                    :class="{'bg-white shadow-sm font-bold text-[#043d2e] ring-1 ring-stone-200/50': tab === 'neraca', 'text-stone-500 font-medium hover:text-[#043d2e] hover:bg-stone-200/50': tab !== 'neraca'}"
                    class="px-5 py-2.5 rounded-lg text-sm transition-all whitespace-nowrap flex-1 md:flex-none flex items-center justify-center gap-2">
                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Laporan Neraca
            </button>
            <button @click="tab = 'phu'" 
                    :class="{'bg-white shadow-sm font-bold text-[#043d2e] ring-1 ring-stone-200/50': tab === 'phu', 'text-stone-500 font-medium hover:text-[#043d2e] hover:bg-stone-200/50': tab !== 'phu'}"
                    class="px-5 py-2.5 rounded-lg text-sm transition-all whitespace-nowrap flex-1 md:flex-none flex items-center justify-center gap-2">
                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Laporan PHU
            </button>
        </div>

        <div class="flex flex-col md:flex-row gap-3 items-center w-full md:w-auto">
            <form method="GET" class="flex gap-3 items-center px-4 py-2 bg-stone-50 border border-stone-200 rounded-xl w-full md:w-auto justify-between md:justify-start shadow-sm">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Tahun Buku</span>
                <select name="tahun" onchange="this.form.submit()" class="bg-transparent border-none text-stone-800 text-sm font-bold py-0.5 outline-none cursor-pointer focus:ring-0">
                    @foreach($tahunBukuList as $th)
                        <option value="{{ $th }}" {{ $th == $tahun ? 'selected' : '' }}>{{ $th }}</option>
                    @endforeach
                </select>
            </form>
            
            <div class="w-px h-8 bg-stone-300 hidden md:block mx-1"></div>

            <button x-show="tab === 'neraca'" @click="$dispatch('open-modal', 'modal-tambah-neraca')" class="w-full md:w-auto flex justify-center items-center gap-2 px-5 py-2.5 bg-[#043d2e] hover:bg-[#065a45] text-white text-sm font-bold rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Parameter
            </button>
            <button style="display: none;" x-show="tab === 'phu'" @click="$dispatch('open-modal', 'modal-tambah-phu')" class="w-full md:w-auto flex justify-center items-center gap-2 px-5 py-2.5 bg-[#043d2e] hover:bg-[#065a45] text-white text-sm font-bold rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Parameter
            </button>
        </div>
    </div>

    {{-- TAB NERACA --}}
    <div x-show="tab === 'neraca'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">
        @if($parameterNeraca->isEmpty())
        <div class="bg-amber-50 py-10 px-6 rounded-2xl border border-amber-200 text-center">
            <svg class="w-16 h-16 text-amber-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
            <h3 class="text-xl text-amber-900 font-black tracking-wide mb-2">Parameter Tahun {{ $tahun }} Masih Kosong</h3>
            <p class="text-sm text-amber-700 mb-8 max-w-lg mx-auto">Sistem belum menemukan definisi parameter untuk neraca di tahun buku ini. Anda bisa membuat satu per satu secara manual, atau langsung menyalin serentak dari tahun {{ $tahun - 1 }}.</p>
            
            <form action="{{ route('keuangan.parameter.copy') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_target" value="{{ $tahun }}">
                <button type="submit" class="inline-flex items-center gap-3 px-6 py-3 bg-[#043d2e] hover:bg-[#065a45] text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-900/20 transition-all hover:scale-105">
                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    Salin Struktur Akun dari Tahun {{ $tahun - 1 }}
                </button>
            </form>
        </div>
        @else
            @foreach(\App\Models\ParameterNeraca::posisiAktiva() as $posisi)
                @if(isset($parameterNeraca[$posisi]))
                    @include('keuangan.parameter.partials._neraca-section', ['posisi' => $posisi, 'items' => $parameterNeraca[$posisi], 'title' => \App\Models\ParameterNeraca::labelPosisi($posisi)])
                @endif
            @endforeach
            
            <div class="h-px bg-stone-300 w-full my-8"></div>

            @foreach(\App\Models\ParameterNeraca::posisiPasiva() as $posisi)
                @if(isset($parameterNeraca[$posisi]))
                    @include('keuangan.parameter.partials._neraca-section', ['posisi' => $posisi, 'items' => $parameterNeraca[$posisi], 'title' => \App\Models\ParameterNeraca::labelPosisi($posisi)])
                @endif
            @endforeach
        @endif
    </div>

    {{-- TAB PHU --}}
    <div x-show="tab === 'phu'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-8">
        @if($parameterPhu->isEmpty())
        <div class="bg-amber-50 py-10 px-6 rounded-2xl border border-amber-200 text-center">
            <svg class="w-16 h-16 text-amber-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
            <h3 class="text-xl text-amber-900 font-black tracking-wide mb-2">Parameter PHU Tahun {{ $tahun }} Masih Kosong</h3>
            <p class="text-sm text-amber-700 mb-8 max-w-lg mx-auto">Sistem belum menemukan definisi parameter untuk PHU di tahun buku ini. Silakan salin struktur akun dari tahun {{ $tahun - 1 }}.</p>
            
            <form action="{{ route('keuangan.parameter.copy') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_target" value="{{ $tahun }}">
                <button type="submit" class="inline-flex items-center gap-3 px-6 py-3 bg-[#043d2e] hover:bg-[#065a45] text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-900/20 transition-all hover:scale-105">
                    <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    Salin Struktur Akun dari Tahun {{ $tahun - 1 }}
                </button>
            </form>
        </div>
        @else
            @foreach(['pendapatan', 'beban'] as $tipe)
                @if(isset($parameterPhu[$tipe]))
                    <div class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden">
                        <div class="bg-stone-50 border-b border-stone-200 px-6 py-4">
                            <h3 class="font-black text-stone-800 uppercase tracking-widest text-sm">{{ ucfirst($tipe) }}</h3>
                        </div>
                        <div class="divide-y divide-stone-100">
                            @foreach($parameterPhu[$tipe] as $item)
                                @include('keuangan.parameter.partials._phu-row', ['item' => $item])
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>



    {{-- MODAL TAMBAH NERACA --}}
    <x-modal name="modal-tambah-neraca" title="Tambah Parameter Neraca ({{ $tahun }})" maxWidth="lg">
        <form action="{{ route('keuangan.parameter.neraca.store') }}" method="POST" class="p-6" x-data="{ sumberData: 'manual', kodeOtomatis: '' }">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <div class="space-y-5">
                <div>
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nama Parameter</label>
                    <input type="text" name="nama" class="w-full border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm py-2.5 bg-stone-50" required>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Posisi Neraca</label>
                    <select name="posisi" class="w-full border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm py-2.5 bg-stone-50">
                        <optgroup label="Aktiva (Aset)">
                            <option value="aktiva_lancar">Harta Lancar</option>
                            <option value="penyertaan">Penyertaan</option>
                            <option value="harta_tetap">Harta Tetap</option>
                            <option value="harta_lain">Harta Lain-lain</option>
                        </optgroup>
                        <optgroup label="Pasiva (Kewajiban & Modal)">
                            <option value="kewajiban_pendek">Kewajiban Jangka Pendek</option>
                            <option value="kewajiban_panjang">Kewajiban Jangka Panjang</option>
                            <option value="modal">Modal Sendiri</option>
                        </optgroup>
                    </select>
                </div>

                <div class="flex items-center gap-2 p-3 bg-stone-50 border border-stone-200 rounded-xl">
                    <input type="checkbox" name="is_pengurang" id="is_pengurang" value="1" class="rounded text-[#043d2e] focus:ring-[#043d2e]/20 border-stone-300 w-4 h-4 cursor-pointer">
                    <label for="is_pengurang" class="text-sm text-stone-700 font-bold cursor-pointer select-none">Tandai sebagai Pengurang (Nilai Minus)</label>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Metode / Sumber Data</label>
                    <select name="sumber_data" x-model="sumberData" class="w-full border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm py-2.5 font-bold text-stone-700 bg-stone-50">
                        <option value="manual">Manual (Isi Statis)</option>
                        <option value="otomatis">Otomatis (Sistem Live)</option>
                    </select>
                </div>

                <div x-show="sumberData === 'otomatis'" x-collapse class="space-y-4">
                    <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-700 uppercase tracking-wider mb-1.5">Kode Resolver Sistem</label>
                            <select name="kode_otomatis" x-model="kodeOtomatis" class="w-full border-emerald-200 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5 bg-white font-bold text-[#043d2e]">
                                <option value="" disabled>-- Pilih Resolver Tersedia --</option>
                                <option value="SALDO_BANK_BRK">Bank BRK Syariah (Master)</option>
                                <option value="SALDO_KAS_TUNAI">Kas Tunai di Tangan (Master)</option>
                                <option value="PIUTANG_PINJAMAN">Piutang Pinjaman Anggota Aktif</option>
                                <option value="PIUTANG_EKSTERNAL">Piutang Lain-Lain (Kustom/Legacy)</option>
                                <option value="DANA_RESIKO_LIVE">Dana Resiko Sistem Pinjaman</option>
                                <option value="SIMPANAN_ANGGOTA_KUSTOM">Simpanan Anggota (Tarik By Jenis)</option>
                                <option value="SHU_TAHUN_BERJALAN">Sisa Hasil Usaha (Berjalan)</option>
                            </select>
                        </div>

                        <div x-show="kodeOtomatis === 'PIUTANG_EKSTERNAL'" x-collapse>
                            <div class="space-y-4 bg-white p-4 rounded-xl border border-emerald-100 shadow-sm mt-2">
                                <p class="text-[11px] font-black uppercase text-emerald-600 tracking-wider flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Filter Piutang Eksternal</p>
                                <div>
                                    <label class="block text-[10px] sm:text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Kategori Peminjam</label>
                                    <select name="kategori_peminjam" class="w-full border-stone-200 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-[13px] bg-stone-50 py-2">
                                        <option value="">Semua Kategori (Global)</option>
                                        <option value="anggota">Anggota (Periode Lalu)</option>
                                        <option value="pengurus">Pengurus</option>
                                        <option value="pihak_ketiga">Pihak Ketiga</option>
                                        <option value="instansi">Instansi</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] sm:text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Alokasi Tahun Pinjam</label>
                                    <select name="tahun_pinjam" class="w-full border-stone-200 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-[13px] bg-stone-50 py-2">
                                        <option value="">Semua Tahun (Digabung)</option>
                                        @for($i = now()->year; $i >= 2015; $i--)
                                            <option value="{{ $i }}">Hanya Tahun {{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div x-show="kodeOtomatis === 'SIMPANAN_ANGGOTA_KUSTOM'" x-collapse>
                            <div class="space-y-4 bg-white p-4 rounded-xl border border-emerald-100 shadow-sm mt-2">
                                <label class="block text-[11px] font-black uppercase text-emerald-600 tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Tarik Dari Simpanan Anggota
                                </label>
                                <div>
                                    <label class="block text-[10px] sm:text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Klasifikasi Jenis Simpanan</label>
                                    <select name="jenis_simpanan_kode" class="w-full border-stone-200 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-[13px] bg-stone-50 py-2">
                                        <option value="">-- Wajib Pilih Jenis --</option>
                                        @foreach($jenisSimpananList as $kjs)
                                            <option value="{{ $kjs->kode }}">{{ $kjs->nama }} ({{ $kjs->kode }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="sumberData === 'manual' || ['SALDO_BANK_BRK','SALDO_KAS_TUNAI','SIMPANAN_LIVE_POKOK','SIMPANAN_LIVE_WAJIB','SIMPANAN_LIVE_SWP'].includes(kodeOtomatis)" x-collapse>
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5" x-text="sumberData === 'manual' ? 'Nominal Manual' : 'Saldo Awal (Opening Balance)'"></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-stone-400 font-bold">Rp</span>
                        </div>
                        <input type="number" name="nominal_manual" value="0" class="w-full pl-11 border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] font-mono text-base font-bold bg-stone-50 py-2.5" min="0" step="1">
                    </div>
                    <p class="text-[10px] text-stone-400 mt-1 uppercase" x-show="sumberData !== 'manual'">Angka ini akan dijumlahkan dengan akumulasi riwayat sistem.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-stone-100">
                <button type="button" @click="show = false" class="px-5 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-700 hover:bg-stone-100 rounded-xl transition-all">Batal</button>
                <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-[#043d2e] hover:bg-[#065a45] rounded-xl transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Parameter
                </button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL TAMBAH PHU --}}
    <x-modal name="modal-tambah-phu" title="Tambah Parameter PHU ({{ $tahun }})" maxWidth="lg">
        <form action="{{ route('keuangan.parameter.phu.store') }}" method="POST" class="p-6" x-data="{ sumberData: 'manual', kodeOtomatis: '' }">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <div class="space-y-5">
                <div>
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nama Parameter</label>
                    <input type="text" name="nama" class="w-full border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm py-2.5 bg-stone-50" required>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Kategori / Tipe</label>
                    <select name="tipe" class="w-full border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm py-2.5 bg-stone-50">
                        <option value="pendapatan">Pendapatan</option>
                        <option value="beban">Beban</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Metode / Sumber Data</label>
                    <select name="sumber_data" x-model="sumberData" class="w-full border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm py-2.5 font-bold text-stone-700 bg-stone-50">
                        <option value="manual">Manual (Isi Statis)</option>
                        <option value="otomatis">Otomatis (Sistem Live)</option>
                    </select>
                </div>

                <div x-show="sumberData === 'otomatis'" x-collapse class="space-y-4">
                    <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl space-y-4">
                        <div>
                            <label class="block text-[11px] font-bold text-emerald-700 uppercase tracking-wider mb-1.5">Kode Resolver Sistem</label>
                            <select name="kode_otomatis" x-model="kodeOtomatis" class="w-full border-emerald-200 rounded-lg shadow-sm focus:ring-emerald-500 focus:border-emerald-500 text-sm py-2.5 bg-white font-bold text-[#043d2e]">
                                <option value="" disabled>-- Pilih Resolver Tersedia --</option>
                                <optgroup label="Pendapatan">
                                    <option value="PENDAPATAN_JASA_PINJAMAN">Pendapatan Jasa Pinjaman</option>
                                    <option value="PENDAPATAN_PROVISI">Pendapatan Administrasi / Provisi</option>
                                    <option value="PENDAPATAN_DANA_RESIKO">Pendapatan Dana Resiko</option>
                                </optgroup>
                                <optgroup label="Beban">
                                    <option value="PHU_BEBAN_OPERASIONAL">Beban Operasional Kas</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                </div>

                <div x-show="sumberData === 'manual'" x-collapse>
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nominal Manual</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-stone-400 font-bold">Rp</span>
                        </div>
                        <input type="number" name="nominal_manual" value="0" class="w-full pl-11 border-stone-200 rounded-xl shadow-sm focus:ring-[#043d2e]/20 focus:border-[#043d2e] font-mono text-base font-bold bg-stone-50 py-2.5" min="0" step="1">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-stone-100">
                <button type="button" @click="show = false" class="px-5 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-700 hover:bg-stone-100 rounded-xl transition-all">Batal</button>
                <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-[#043d2e] hover:bg-[#065a45] rounded-xl transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Parameter
                </button>
            </div>
        </form>
    </x-modal>

</div>
@endsection
