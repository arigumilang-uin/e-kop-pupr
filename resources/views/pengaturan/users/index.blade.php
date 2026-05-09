@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('subtitle', 'Akun pengguna yang dapat mengakses sistem')

@section('actions')
    @can('user.create')
    <button type="button" x-data @click="$dispatch('open-modal', 'modal-tambah-user')" class="py-2.5 px-4 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Tambah Pengguna</span>
    </button>
    @endcan
@endsection

@php use App\Services\PermissionRegistry; @endphp

@section('content')
<div>
    <div x-data="userFilter()" class="flex flex-col gap-5">
        <x-filter-bar searchPlaceholder="Cari nama, username, atau NIP..." x-model="q">
            <x-slot name="indicator">
                <template x-if="activeFiltersCount > 0">
                    <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 border-2 border-white rounded-full shadow-sm"></span>
                </template>
            </x-slot>
            <x-slot name="filters">
                <div class="flex flex-col gap-1.5">
                    <label class="text-[11px] font-bold text-stone-500 uppercase tracking-wider">Role</label>
                    <select x-model="role" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] outline-none text-stone-700 cursor-pointer appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a8a29e%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:10px_10px] bg-no-repeat bg-[position:right_12px_center] pr-8 shadow-sm">
                        <option value="">Semua Role</option>
                        @foreach($roles as $r)
                        <option value="{{ $r->name }}">{{ PermissionRegistry::roleLabel($r->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" @click="resetFilter()" x-show="activeFiltersCount > 0" class="mt-2 w-full py-2.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-sm font-bold transition-colors border border-red-200">Reset Filter</button>
            </x-slot>
        </x-filter-bar>

        <div id="table-content-container" class="relative">
            <x-table>
                <x-table.thead>
                    <tr>
                        <x-table.th>Nama</x-table.th>
                        <x-table.th>Username</x-table.th>
                        <x-table.th>Role</x-table.th>
                        <x-table.th>NIP</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Login Terakhir</x-table.th>
                        @canany(['user.edit', 'user.deactivate'])
                        <x-table.th class="text-right">Aksi</x-table.th>
                        @endcanany
                    </tr>
                </x-table.thead>
                <x-table.tbody>
                    @forelse($users as $u)
                    @php
                        $userRole = $u->getRoleNames()->first() ?? 'unknown';
                        $rc = match($userRole) {
                            'super_admin' => ['bg-violet-100 text-violet-700 border-violet-200', 'bg-violet-600'],
                            'admin'       => ['bg-[#043d2e]/10 text-[#043d2e] border-[#043d2e]/20', 'bg-[#043d2e]'],
                            'pimpinan'    => ['bg-amber-100 text-amber-700 border-amber-200', 'bg-amber-600'],
                            default       => ['bg-stone-100 text-stone-600 border-stone-200', 'bg-stone-500'],
                        };
                    @endphp
                    <x-table.tr class="hover:bg-stone-50/50 transition-colors {{ !$u->is_active ? 'opacity-50' : '' }}">
                        <x-table.td class="align-top">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold text-white shrink-0 shadow-sm {{ $rc[1] }}">{{ strtoupper(substr($u->nama, 0, 1)) }}</div>
                                <span class="font-bold text-stone-800 text-[13px]">{{ $u->nama }}</span>
                            </div>
                        </x-table.td>
                        <x-table.td class="align-top"><span class="font-mono text-[13px] text-stone-600">{{ $u->username }}</span></x-table.td>
                        <x-table.td class="align-top"><span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-bold border {{ $rc[0] }}">{{ PermissionRegistry::roleLabel($userRole) }}</span></x-table.td>
                        <x-table.td class="align-top">
                            @if($u->nip)
                                <span class="font-mono text-[13px] text-stone-700">{{ $u->nip }}</span>
                            @else
                                <span class="text-[12px] text-stone-400 italic">—</span>
                            @endif
                        </x-table.td>
                        <x-table.td class="align-top">
                            @if($u->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-red-50 text-red-600 border border-red-200">Nonaktif</span>
                            @endif
                        </x-table.td>
                        <x-table.td class="align-top">
                            @if($u->last_login_at)
                                <span class="text-[12px] text-stone-500">{{ $u->last_login_at->diffForHumans() }}</span>
                            @else
                                <span class="text-[12px] text-stone-400 italic">Belum pernah</span>
                            @endif
                        </x-table.td>
                        @canany(['user.edit', 'user.deactivate'])
                        <x-table.td class="whitespace-nowrap text-right align-top">
                            <x-action-dropdown>
                                @can('user.edit')
                                <x-action-dropdown-item type="button" onclick="document.querySelector('#modal-edit-user-{{ $u->id }}').dispatchEvent(new CustomEvent('open-modal', {bubbles:true, detail:'modal-edit-user-{{ $u->id }}'}))" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>'>
                                    Edit
                                </x-action-dropdown-item>
                                @endcan
                                @can('user.deactivate')
                                @if($u->id !== auth()->id())
                                <form action="{{ route('users.toggleActive', $u->id) }}" method="POST" onsubmit="return confirm('{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan kembali' }} akun {{ $u->nama }}?')">
                                    @csrf @method('PATCH')
                                    <x-action-dropdown-item type="button" color="{{ $u->is_active ? 'red' : 'green' }}" onclick="this.closest('form').submit()" icon='<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $u->is_active ? "M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" : "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" }}"/>'>
                                        {{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </x-action-dropdown-item>
                                </form>
                                @endif
                                @endcan
                            </x-action-dropdown>
                        </x-table.td>
                        @endcanany
                    </x-table.tr>

                    {{-- Edit Modal per user --}}
                    @can('user.edit')
                    <x-modal name="modal-edit-user-{{ $u->id }}" title="Edit Pengguna" subtitle="{{ $u->nama }}" maxWidth="lg">
                        <form id="form-edit-user-{{ $u->id }}" action="{{ route('users.update', $u->id) }}" method="POST" class="contents" x-data="userForm('{{ $u->nip }}')">
                            @csrf @method('PUT')
                            @include('pengaturan.users._form', ['user' => $u, 'formId' => 'form-edit-user-'.$u->id, 'isEdit' => true])
                        </form>
                    </x-modal>
                    @endcan

                    @empty
                    <x-table.tr>
                        <x-table.td colspan="7" class="px-6 py-12 text-center text-stone-500 font-medium text-sm">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                Belum ada data pengguna.
                            </div>
                        </x-table.td>
                    </x-table.tr>
                    @endforelse
                </x-table.tbody>
            </x-table>

            @if($users->hasPages())
            <div class="px-6 py-4 border-t border-stone-200 bg-stone-50/50">{{ $users->links() }}</div>
            @endif

            <div x-show="loading" x-transition.opacity class="absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center z-20">
                <div class="flex items-center gap-3 px-5 py-3 bg-white rounded-2xl shadow-xl border border-stone-200">
                    <svg class="animate-spin h-5 w-5 text-[#043d2e]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="font-bold text-stone-700 text-sm">Memuat data...</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah User --}}
@can('user.create')
<x-modal name="modal-tambah-user" title="Tambah Pengguna Baru" maxWidth="lg">
    <form id="form-tambah-user" action="{{ route('users.store') }}" method="POST" class="contents" x-data="userForm('')">
        @csrf
        @include('pengaturan.users._form', ['user' => null, 'formId' => 'form-tambah-user', 'isEdit' => false])
    </form>
</x-modal>
@endcan

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('userForm', (initialNip) => ({
        nip: initialNip || '',
        get hasNip() { return this.nip.trim() !== ''; },
    }));

    Alpine.data('userFilter', () => ({
        q: new URLSearchParams(location.search).get('q') || '',
        role: new URLSearchParams(location.search).get('role') || '',
        loading: false, timeout: null, abortController: null,
        init() {
            this.$watch('q', () => this.debouncedFetch());
            this.$watch('role', () => this.fetchData());
        },
        get activeFiltersCount() { return (this.q !== '' ? 1 : 0) + (this.role !== '' ? 1 : 0); },
        resetFilter() { this.q = ''; this.role = ''; this.fetchData(); },
        debouncedFetch() { clearTimeout(this.timeout); this.timeout = setTimeout(() => this.fetchData(), 400); },
        async fetchData(targetUrl = null) {
            this.loading = true;
            if (this.abortController) this.abortController.abort();
            this.abortController = new AbortController();
            let url = targetUrl;
            if (!url) {
                const params = new URLSearchParams();
                if (this.q !== '') params.append('q', this.q);
                if (this.role !== '') params.append('role', this.role);
                url = `${window.location.pathname}?${params.toString()}`;
            }
            try {
                window.history.pushState({}, '', url);
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: this.abortController.signal });
                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const nc = doc.getElementById('table-content-container');
                if (nc) document.getElementById('table-content-container').innerHTML = nc.innerHTML;
            } catch (e) { if (e.name !== 'AbortError') console.error(e); }
            finally { this.loading = false; }
        }
    }));
});
</script>
@endpush
@endsection
