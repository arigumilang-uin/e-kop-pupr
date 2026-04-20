@extends('layouts.app')

@section('title', 'Master Data Anggota')
@section('subtitle', 'Daftar keseluruhan anggota Koperasi PUPR Provinsi Riau')

@section('actions')
<a href="{{ route('anggota.create') }}" class="py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
    </svg>
    Tambah Anggota
</a>
@endsection

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    {{-- Filter Bar --}}
    <div class="p-4 sm:p-5 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row gap-4 justify-between items-center">
        <form action="{{ route('anggota.index') }}" method="GET" class="w-full sm:max-w-xs relative">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari NIP atau Nama..."
                   class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </form>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 text-slate-500 font-medium border-b border-slate-200 text-[13px] uppercase tracking-wider">
                <tr>
                    <th scope="col" class="px-6 py-4">Informasi Pegawai</th>
                    <th scope="col" class="px-6 py-4">Bidang / Jabatan</th>
                    <th scope="col" class="px-6 py-4">Status</th>
                    <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($anggotas as $anggota)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-semibold text-slate-800">{{ $anggota->nama }}</span>
                            <span class="text-xs text-slate-500 font-mono mt-0.5">NIP. {{ $anggota->nip }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-slate-800">{{ $anggota->bidang ? $anggota->bidang->nama_bidang : '-' }}</span>
                            <span class="text-xs text-slate-500 mt-0.5">{{ $anggota->jabatan ?: '-' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($anggota->status->value === 'aktif')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-600 border border-slate-300 text-xs font-semibold">
                            Non-Aktif
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('anggota.edit', $anggota->id) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Data">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('anggota.destroy', $anggota->id) }}" method="POST" onsubmit="return confirm('Yakin ingin merubah status aktif anggota ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Ubah Status">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                        Belum ada data anggota yang terdaftar atau ditemukan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($anggotas->hasPages())
    <div class="px-6 py-4 border-t border-slate-200">
        {{ $anggotas->links() }}
    </div>
    @endif
</div>
@endsection
