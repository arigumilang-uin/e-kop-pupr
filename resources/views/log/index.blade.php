@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('subtitle', 'Riwayat seluruh aksi yang tercatat di dalam sistem')

@section('content')
<div class="space-y-6">

    {{-- Filter Bar --}}
    <form x-data x-ref="form" action="{{ route('log.index') }}" method="GET">
        <x-filter-bar 
            name="q" 
            value="{{ request('q') }}" 
            searchPlaceholder="Ketik kata kunci untuk mencari log..." 
            @input.debounce.500ms="$refs.form.submit()"
        >
            <x-slot name="filters">
                <div>
                    <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-2">Jenis Aktivitas</label>
                    <select name="aktivitas" @change="$refs.form.submit()" class="w-full px-4 py-2.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 rounded-xl text-sm font-medium text-stone-700 focus:ring-4 focus:ring-[#043d2e]/10 focus:border-[#043d2e] transition-all cursor-pointer outline-none">
                        <option value="">Semua Aktivitas</option>
                        @foreach($aktivitasList as $akt)
                            <option value="{{ $akt }}" {{ request('aktivitas') === $akt ? 'selected' : '' }}>{{ strtoupper($akt) }}</option>
                        @endforeach
                    </select>
                </div>
                
                @if(request('aktivitas') || request('q'))
                <div class="pt-2">
                    <a href="{{ route('log.index') }}" class="w-full inline-flex items-center justify-center h-11 bg-white hover:bg-red-50 text-stone-500 hover:text-red-600 border border-stone-200 hover:border-red-200 rounded-xl text-sm font-bold transition-all gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset Filter
                    </a>
                </div>
                @endif
            </x-slot>

            @if(request('aktivitas'))
            <x-slot name="indicator">
                <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
            </x-slot>
            @endif
        </x-filter-bar>
    </form>

    {{-- Log Table --}}
    <div class="relative bg-white border border-stone-200 rounded-2xl shadow-sm">
        <x-table>
            <x-table.thead>
                <x-table.th class="w-40">Waktu</x-table.th>
                <x-table.th class="w-48">Pengguna</x-table.th>
                <x-table.th class="w-40">Aktivitas</x-table.th>
                <x-table.th>Deskripsi</x-table.th>
                <x-table.th class="w-36 text-center">IP Address</x-table.th>
            </x-table.thead>
            <x-table.tbody>
                @forelse($logs as $log)
                <x-table.tr class="group">
                    <x-table.td>
                        <div class="text-[13px] font-bold text-stone-700">{{ $log->created_at->format('d M Y') }}</div>
                        <div class="text-[11px] font-mono font-medium text-stone-500 mt-0.5">{{ $log->created_at->format('H:i:s') }} WIB</div>
                    </x-table.td>
                    <x-table.td>
                        @if($log->user)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-[#043d2e]/10 flex items-center justify-center text-[12px] font-bold text-[#043d2e] shrink-0 border border-[#043d2e]/20 group-hover:scale-110 transition-transform">
                                {{ strtoupper(substr($log->user->nama, 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <h4 class="text-[13px] font-bold text-stone-800 truncate">{{ $log->user->nama }}</h4>
                                <p class="text-[11px] text-stone-500 font-mono truncate">{{ $log->user->username }}</p>
                            </div>
                        </div>
                        @else
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-stone-100 flex items-center justify-center text-[12px] font-bold text-stone-500 shrink-0 border border-stone-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span class="text-[13px] font-bold text-stone-500">Sistem Autentikasi</span>
                        </div>
                        @endif
                    </x-table.td>
                    <x-table.td>
                        @php
                            $badgeClass = match(true) {
                                str_contains($log->aktivitas, 'login') => 'bg-[#043d2e] text-white border-[#043d2e]',
                                str_contains($log->aktivitas, 'logout') => 'bg-stone-200 text-stone-700 border-stone-300',
                                str_contains($log->aktivitas, 'approved') || str_contains($log->aktivitas, 'paid') || str_contains($log->aktivitas, 'tpp') => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                str_contains($log->aktivitas, 'rejected') || str_contains($log->aktivitas, 'terkunci') || str_contains($log->aktivitas, 'deleted') || str_contains($log->aktivitas, 'void') => 'bg-red-50 text-red-700 border-red-200',
                                str_contains($log->aktivitas, 'created') => 'bg-blue-50 text-blue-700 border-blue-200',
                                str_contains($log->aktivitas, 'updated') => 'bg-amber-50 text-amber-800 border-amber-300',
                                default => 'bg-stone-50 text-stone-600 border-stone-200',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider border {{ $badgeClass }}">
                            {{ $log->aktivitas }}
                        </span>
                    </x-table.td>
                    <x-table.td>
                        <p class="text-[13px] text-stone-700 leading-relaxed font-medium line-clamp-2" title="{{ $log->deskripsi }}">
                            {{ $log->deskripsi }}
                        </p>
                    </x-table.td>
                    <x-table.td class="text-center">
                        <span class="inline-flex px-2 py-1 bg-stone-100 text-stone-500 border border-stone-200 rounded font-mono text-[11px] font-medium">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </span>
                    </x-table.td>
                </x-table.tr>
                @empty
                <x-table.tr>
                    <x-table.td colspan="5" class="py-16 text-center">
                        <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-stone-200">
                            <svg class="w-8 h-8 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <h3 class="text-[15px] font-bold text-stone-800 mb-1">Belum Ada Log Aktivitas</h3>
                        <p class="text-[13px] text-stone-500">Tidak ada riwayat aktivitas yang sesuai dengan kriteria filter.</p>
                    </x-table.td>
                </x-table.tr>
                @endforelse
            </x-table.tbody>
        </x-table>

        @if($logs->hasPages())
        <div class="px-5 py-4 border-t border-stone-200 bg-stone-50 rounded-b-2xl">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
