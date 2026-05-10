@extends('layouts.app')

@section('title', 'Arsip Laporan')
@section('subtitle', 'Daftar laporan yang pernah di-generate oleh sistem')

@section('content')
<div x-data="{ q: new URLSearchParams(location.search).get('q') || '', tipe: new URLSearchParams(location.search).get('tipe') || '' }">
    <div class="flex flex-col gap-5">
        <x-filter-bar searchPlaceholder="Cari nama file atau keterangan..." x-model="q">
            <x-slot name="filters">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Tipe Laporan</label>
                    <select x-model="tipe" @change="window.location.href = '?q=' + q + '&tipe=' + tipe" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                        <option value="">Semua Tipe</option>
                        @foreach($tipes as $t)
                        <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" @click="q = ''; tipe = ''; window.location.href = '?'" x-show="q || tipe" class="mt-2 w-full py-2.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-sm font-bold transition-colors border border-red-200">Reset Filter</button>
            </x-slot>
        </x-filter-bar>

        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden flex flex-col">
            <x-table>
                <x-table.thead :sticky="false">
                    <x-table.th>Tipe Laporan</x-table.th>
                    <x-table.th>Nama File</x-table.th>
                    <x-table.th>Keterangan Filter</x-table.th>
                    <x-table.th>Dibuat Oleh</x-table.th>
                    <x-table.th>Tanggal Generate</x-table.th>
                    <x-table.th class="text-right">Aksi</x-table.th>
                </x-table.thead>
                <x-table.tbody>
                    @forelse($arsip as $item)
                    <x-table.tr class="hover:bg-stone-50/50 transition-colors">
                        <x-table.td class="align-top">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[11px] font-bold border border-stone-200 bg-stone-100 text-stone-700">
                                {{ $item->tipe_laporan }}
                            </span>
                        </x-table.td>
                        <x-table.td class="align-top">
                            <div class="flex items-center gap-2">
                                @if($item->format === 'PDF')
                                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M11.5,12.5C11.5,11.5 12,11 12.5,11C13.5,11 14,11.5 14,12.5C14,13.5 13.5,14 12.5,14C12,14 11.5,13.5 11.5,12.5M20,10.5C20,9.5 19.5,9 18.5,9C17.5,9 17,9.5 17,10.5V13.5C17,14.5 17.5,15 18.5,15C19.5,15 20,14.5 20,13.5V10.5M8.5,11.5H7V13.5H8.5C9.5,13.5 10,13 10,12.5C10,11.5 9.5,11.5 8.5,11.5M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M11.5,15.5V17H10V9H12.5C14.5,9 15.5,10 15.5,12.5C15.5,15 14.5,15.5 12.5,15.5H11.5M21.5,13.5C21.5,15.5 20.5,16.5 18.5,16.5H17V17H15.5V9H18.5C20.5,9 21.5,10 21.5,12.5V13.5M8.5,15H5.5V17H4V9H8.5C10.5,9 11.5,10 11.5,12.5C11.5,14 10.5,15 8.5,15Z"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M21.17 3.25Q21.5 3.25 21.76 3.5 22 3.74 22 4.08V19.92Q22 20.26 21.76 20.5 21.5 20.75 21.17 20.75H7.83Q7.5 20.75 7.24 20.5 7 20.26 7 19.92V17H2.83Q2.5 17 2.24 16.76 2 16.5 2 16.17V7.83Q2 7.5 2.24 7.24 2.5 7 2.83 7H7V4.08Q7 3.74 7.24 3.5 7.5 3.25 7.83 3.25M7 13.06L8.18 15.28H9.97L8 12.06L9.93 8.89H8.22L7.13 10.9L6.04 8.89H4.26L6.19 12.06L4.22 15.28H5.96M17 9H11V11H17M20 9H18.5V11H20M17 12H11V14H17M20 12H18.5V14H20M17 15H11V17H17M20 15H18.5V17H20Z"/></svg>
                                @endif
                                <span class="font-bold text-[13px] text-stone-800">{{ $item->nama_file }}</span>
                                @if($item->is_permanent)
                                    <span class="inline-flex items-center justify-center p-1 rounded-full bg-amber-100 text-amber-600 tooltip" title="Arsip Permanen (Di-Pin)">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/></svg>
                                    </span>
                                @endif
                            </div>
                        </x-table.td>
                        <x-table.td class="align-top">
                            <span class="text-[12px] text-stone-600 font-medium">{{ $item->filter_info ?: '-' }}</span>
                        </x-table.td>
                        <x-table.td class="align-top">
                            <span class="text-[12px] text-stone-700 font-bold">{{ $item->user->nama ?? 'Sistem' }}</span>
                        </x-table.td>
                        <x-table.td class="align-top">
                            <p class="text-[12px] text-stone-700 font-bold mb-0.5">{{ $item->created_at->format('d M Y H:i') }}</p>
                            @if(!$item->is_permanent)
                                @php
                                    $sisaHari = max(0, 30 - (int) $item->created_at->diffInDays(now()));
                                @endphp
                                @if($sisaHari <= 5)
                                    <span class="text-[10px] text-red-500 font-bold tracking-wide">Dihapus otomatis dlm {{ max(0, $sisaHari) }} hari</span>
                                @else
                                    <span class="text-[10px] text-stone-400">Dihapus otomatis dlm {{ $sisaHari }} hari</span>
                                @endif
                            @else
                                <span class="text-[10px] text-emerald-600 font-bold tracking-wide">Arsip Permanen</span>
                            @endif
                        </x-table.td>
                        <x-table.td class="whitespace-nowrap text-right align-top">
                            <x-action-dropdown>
                                <x-action-dropdown-item type="link" href="{{ route('arsip.download', $item->id) }}" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>'>
                                    Download File
                                </x-action-dropdown-item>
                                
                                @can('arsip.manage')
                                <form action="{{ route('arsip.toggle-pin', $item->id) }}" method="POST" class="border-b border-stone-100">
                                    @csrf @method('PATCH')
                                    <x-action-dropdown-item type="button" onclick="this.closest('form').submit()" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>'>
                                        {{ $item->is_permanent ? 'Lepas Pin Permanen' : 'Jadikan Permanen (Pin)' }}
                                    </x-action-dropdown-item>
                                </form>
                                <form action="{{ route('arsip.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus arsip ini secara permanen?')">
                                    @csrf @method('DELETE')
                                    <x-action-dropdown-item type="button" color="red" onclick="this.closest('form').submit()" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>'>
                                        Hapus Arsip
                                    </x-action-dropdown-item>
                                </form>
                                @endcan
                            </x-action-dropdown>
                        </x-table.td>
                    </x-table.tr>
                    @empty
                    <x-table.tr>
                        <x-table.td colspan="6" class="px-6 py-12 text-center text-stone-500 font-medium text-sm">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Belum ada arsip laporan.
                            </div>
                        </x-table.td>
                    </x-table.tr>
                    @endforelse
                </x-table.tbody>
            </x-table>
            
            @if($arsip->hasPages())
            <div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">
                {{ $arsip->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
