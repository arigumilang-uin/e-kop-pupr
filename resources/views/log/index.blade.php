@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('subtitle', 'Riwayat seluruh aksi yang tercatat di dalam sistem')

@section('content')
<div class="space-y-6">

    {{-- Filter Bar --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-5">
        <form action="{{ route('log.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="w-full sm:w-56">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Jenis Aktivitas</label>
                <select name="aktivitas" class="w-full px-4 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Semua Aktivitas --</option>
                    @foreach($aktivitasList as $akt)
                        <option value="{{ $akt }}" {{ request('aktivitas') === $akt ? 'selected' : '' }}>{{ $akt }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full sm:flex-1 relative">
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Cari Deskripsi</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Kata kunci..."
                       class="w-full pl-10 pr-4 py-2 bg-white border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 bottom-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="py-2 px-5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors">
                    Filter
                </button>
                <a href="{{ route('log.index') }}" class="py-2 px-4 bg-white border border-slate-300 text-slate-600 rounded-xl text-sm font-medium hover:bg-slate-50 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Log Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-4 font-semibold text-slate-600 w-44">Waktu</th>
                        <th class="px-5 py-4 font-semibold text-slate-600 w-36">Pengguna</th>
                        <th class="px-5 py-4 font-semibold text-slate-600 w-40">Aktivitas</th>
                        <th class="px-5 py-4 font-semibold text-slate-600">Deskripsi</th>
                        <th class="px-5 py-4 font-semibold text-slate-600 w-32">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3 whitespace-nowrap">
                            <div class="text-xs text-slate-500">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-xs font-mono text-slate-400">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap">
                            @if($log->user)
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center text-[10px] font-bold text-white shrink-0">
                                    {{ strtoupper(substr($log->user->nama, 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium text-slate-700 truncate max-w-[100px]">{{ $log->user->nama }}</span>
                            </div>
                            @else
                            <span class="text-xs text-slate-400 italic">Sistem</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap">
                            @php
                                $badgeColor = match(true) {
                                    str_contains($log->aktivitas, 'login') => 'bg-blue-50 text-blue-700 border-blue-200',
                                    str_contains($log->aktivitas, 'logout') => 'bg-slate-100 text-slate-600 border-slate-200',
                                    str_contains($log->aktivitas, 'approved') || str_contains($log->aktivitas, 'paid') || str_contains($log->aktivitas, 'tpp') => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    str_contains($log->aktivitas, 'rejected') || str_contains($log->aktivitas, 'terkunci') => 'bg-red-50 text-red-700 border-red-200',
                                    str_contains($log->aktivitas, 'created') => 'bg-violet-50 text-violet-700 border-violet-200',
                                    str_contains($log->aktivitas, 'updated') => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border {{ $badgeColor }}">
                                {{ $log->aktivitas }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <p class="text-sm text-slate-700 line-clamp-2">{{ $log->deskripsi }}</p>
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap">
                            <span class="text-xs font-mono text-slate-400">{{ $log->ip_address ?? '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            <p class="text-slate-500 text-sm">Belum ada log aktivitas yang tercatat.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50/50">
            {{ $logs->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
