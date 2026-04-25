@extends('layouts.app')

@section('title', 'Pengeluaran Kas Manual')
@section('subtitle', 'Catat beban operasional, belanja, dan pengeluaran lain di luar pinjaman anggota')

@section('content')
<div class="space-y-6">

    {{-- Kategori Box & Tambah Pengeluaran --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Kategori Pengeluaran List --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-800">Master Kategori</h3>
                    <button onclick="document.getElementById('modal-kategori').classList.remove('hidden')" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg font-medium transition-colors">
                        + Kategori
                    </button>
                </div>
                
                @if($kategori->isEmpty())
                <div class="text-center py-6">
                    <p class="text-xs text-slate-400">Belum ada kategori pengeluaran.</p>
                </div>
                @else
                <ul class="space-y-2">
                    <li class="flex items-center justify-between">
                        <a href="{{ route('pengeluaran.index') }}" class="text-sm font-medium {{ !request('kategori_id') ? 'text-blue-600' : 'text-slate-600 hover:text-blue-500' }}">Semua Kategori</a>
                    </li>
                    @foreach($kategori as $kat)
                    <li class="flex items-center justify-between group">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                            <a href="{{ route('pengeluaran.index', ['kategori_id' => $kat->id]) }}" class="text-sm font-medium {{ request('kategori_id') == $kat->id ? 'text-amber-600' : 'text-slate-600 hover:text-amber-500' }}">
                                {{ $kat->nama }}
                            </a>
                        </div>
                        <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">{{ $kat->pengeluaran_kas_count }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>

            {{-- Summary Box --}}
            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl p-5 text-white shadow-lg shadow-red-500/20">
                <p class="text-red-100 text-xs font-medium mb-1">Total Pengeluaran Kas (Seluruhnya)</p>
                <p class="text-2xl font-bold font-mono">{{ format_rupiah($totalPengeluaran) }}</p>
            </div>
        </div>

        {{-- Form & Tabel Pengeluaran --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Form Input --}}
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Catat Pengeluaran Baru
                </h3>

                <form action="{{ route('pengeluaran.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Pilih Kategori</label>
                            <select name="kategori_pengeluaran_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm">
                                <option value="">-- Pilih --</option>
                                @foreach($kategori as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_pengeluaran_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                            @error('kategori_pengeluaran_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Nominal (Rp)</label>
                            <input type="number" name="nominal" required min="1" value="{{ old('nominal') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm" placeholder="Contoh: 150000">
                            @error('nominal') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Tanggal Keluar</label>
                            <input type="date" name="tanggal" required value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm">
                            @error('tanggal') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Keterangan / Tujuan</label>
                            <input type="text" name="keterangan" required value="{{ old('keterangan') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm" placeholder="Contoh: Beli kertas A4 2 rim">
                            @error('keterangan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition-colors shadow-sm">
                            Simpan Pengeluaran
                        </button>
                    </div>
                </form>
            </div>

            {{-- Table Histori --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-800">Riwayat Pengeluaran</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-xs font-semibold text-slate-500">Tanggal</th>
                                <th class="px-5 py-3 text-xs font-semibold text-slate-500">Kategori</th>
                                <th class="px-5 py-3 text-xs font-semibold text-slate-500">Keterangan</th>
                                <th class="px-5 py-3 text-xs font-semibold text-slate-500 text-right">Nominal</th>
                                <th class="px-5 py-3 text-xs font-semibold text-slate-500"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($pengeluaran as $p)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-5 py-3 text-slate-500 whitespace-nowrap">{{ $p->tanggal->translatedFormat('d M Y') }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                                        {{ $p->kategori->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-slate-700">{{ $p->keterangan }}</td>
                                <td class="px-5 py-3 text-right font-mono font-bold text-red-600 whitespace-nowrap">{{ format_rupiah($p->nominal) }}</td>
                                <td class="px-5 py-3 text-right">
                                    <form action="{{ route('pengeluaran.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan pengeluaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors p-1" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-slate-400">Belum ada riwayat pengeluaran kas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $pengeluaran->links() }}
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Modal Tambah Kategori --}}
<div id="modal-kategori" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity backdrop-blur-sm bg-slate-900/60" aria-hidden="true" onclick="document.getElementById('modal-kategori').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full border border-slate-100 relative z-10">
            <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    Tambah Kategori Pengeluaran
                </h3>
            </div>
            <form action="{{ route('pengeluaran.kategori.store') }}" method="POST">
                @csrf
                <div class="px-6 py-5 bg-slate-50/50 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kategori</label>
                        <input type="text" name="nama" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Contoh: ATK">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" placeholder="Contoh: Pembelian alat tulis kantor"></textarea>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm transition-colors">Simpan</button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors" onclick="document.getElementById('modal-kategori').classList.add('hidden')">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
