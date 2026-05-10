@extends('layouts.app')

@section('title', 'Manajemen Role')
@section('subtitle', 'Kelola role dan permission akses sistem')

@php
    use App\Services\PermissionRegistry;
@endphp

@section('actions')
    @can('role.create')
    <button type="button" x-data @click="$dispatch('open-modal', 'modal-tambah-role')" class="py-2.5 px-4 rounded-xl bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold transition-colors flex items-center gap-2 shadow-sm">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        <span>Tambah Role</span>
    </button>
    @endcan
@endsection

@section('content')
<div class="flex flex-col gap-6">

    {{-- Role Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($roles as $role)
        @php
            $isProtected = in_array($role->name, PermissionRegistry::PROTECTED_ROLES);
            $isSuperAdmin = $role->name === PermissionRegistry::ROLE_SUPER_ADMIN;
            $isCore = in_array($role->name, ['super_admin', 'admin', 'pimpinan']);
            
            $cardColor = $isCore ? 'border-[#043d2e] bg-[#043d2e]' : 'border-stone-200 bg-white';
            $badgeColor = $isCore ? 'bg-white/10 border-white/20' : 'bg-stone-50 border-stone-200';
            $iconColor = $isCore ? 'text-white' : 'text-stone-500';
            
            $titleColor = $isCore ? 'text-white' : 'text-stone-800';
            $subtitleColor = $isCore ? 'text-emerald-100/70' : 'text-stone-400';
            $statsColor = $isCore ? 'text-white' : 'text-stone-700';
            $statsIconColor = $isCore ? 'text-emerald-100/70' : 'text-stone-500';
            $badgeIntiColor = $isCore ? 'bg-white/10 text-white border-white/20' : 'bg-stone-100 text-stone-500 border-stone-200';
            
            $actionBgColor = $isCore ? 'bg-[#033024] border-white/10' : 'bg-stone-50/80 border-stone-100';
            $btnEditColor = $isCore ? 'text-emerald-100 hover:text-white hover:bg-white/10' : 'text-stone-600 hover:text-[#043d2e] hover:bg-[#043d2e]/5';
            $btnDeleteColor = $isCore ? 'text-red-300 hover:text-red-200 hover:bg-red-500/20' : 'text-red-500 hover:text-red-700 hover:bg-red-50';
            $infoColor = $isCore ? 'text-emerald-100/70' : 'text-stone-400';
        @endphp
        <div class="rounded-2xl border {{ $cardColor }} shadow-sm overflow-hidden hover:shadow-md transition-shadow">
            {{-- Card Header --}}
            <div class="px-5 pt-5 pb-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $badgeColor }} border">
                            <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($isSuperAdmin)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                @endif
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold {{ $titleColor }} text-[15px]">{{ PermissionRegistry::roleLabel($role->name) }}</h3>
                            <p class="text-[11px] {{ $subtitleColor }} font-mono mt-0.5">{{ $role->name }}</p>
                        </div>
                    </div>
                    @if($isProtected)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold {{ $badgeIntiColor }} shrink-0">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Inti
                    </span>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="flex items-center gap-4 mt-4">
                    <button type="button" x-data @click="$dispatch('open-modal', 'modal-info-users-{{ $role->id }}')" class="flex items-center gap-1.5 text-[12px] {{ $statsIconColor }} hover:opacity-80 transition-opacity group">
                        <svg class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="font-bold {{ $statsColor }} group-hover:underline">{{ $role->users_count }}</span> pengguna
                    </button>
                    <button type="button" x-data @click="$dispatch('open-modal', 'modal-info-permissions-{{ $role->id }}')" class="flex items-center gap-1.5 text-[12px] {{ $statsIconColor }} hover:opacity-80 transition-opacity group">
                        <svg class="w-3.5 h-3.5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        <span class="font-bold {{ $statsColor }} group-hover:underline">{{ $role->permissions_count }}</span> permission
                    </button>
                </div>
            </div>

            {{-- Card Actions --}}
            <div class="px-5 py-3 border-t flex items-center justify-end gap-2 {{ $actionBgColor }}">
                @if(!$isSuperAdmin)
                @can('role.edit')
                <button type="button" x-data @click="$dispatch('open-modal', 'modal-edit-role-{{ $role->id }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[12px] font-bold rounded-lg transition-colors {{ $btnEditColor }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Permissions
                </button>
                @endcan
                @endif

                @if(!$isProtected)
                @can('role.delete')
                <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Hapus role \'{{ $role->name }}\'? Aksi ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[12px] font-bold rounded-lg transition-colors {{ $btnDeleteColor }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus
                    </button>
                </form>
                @endcan
                @endif

                @if($isSuperAdmin)
                <span class="text-[11px] italic {{ $infoColor }}">Tidak dapat diedit</span>
                @endif
            </div>
        </div>

        {{-- Edit Modal for each non-super_admin role --}}
        @if(!$isSuperAdmin)
        <x-modal name="modal-edit-role-{{ $role->id }}" title="Edit Role: {{ PermissionRegistry::roleLabel($role->name) }}" subtitle="Atur permission yang dimiliki role ini" maxWidth="3xl">
            <form id="form-edit-role-{{ $role->id }}" action="{{ route('roles.update', $role) }}" method="POST" class="contents">
                @csrf
                @method('PUT')

                {{-- Role Name (readonly for protected, editable for custom) --}}
                <div class="mb-5">
                    <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nama Role</label>
                    @if($isProtected)
                        <div class="px-4 py-2.5 rounded-xl border border-stone-200 bg-stone-100 text-sm text-stone-500 font-mono cursor-not-allowed">
                            {{ $role->name }}
                            <span class="text-[10px] text-stone-400 ml-2">(tidak dapat diubah)</span>
                        </div>
                    @else
                        <input type="text" name="name" value="{{ $role->name }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all font-mono" placeholder="contoh: bendahara">
                    @endif
                </div>

                {{-- Permission Matrix --}}
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider">Permissions</label>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="toggleAllCheckboxes('form-edit-role-{{ $role->id }}', true)" class="text-[11px] font-bold text-[#043d2e] hover:underline">Pilih Semua</button>
                            <span class="text-stone-300">|</span>
                            <button type="button" onclick="toggleAllCheckboxes('form-edit-role-{{ $role->id }}', false)" class="text-[11px] font-bold text-red-500 hover:underline">Hapus Semua</button>
                        </div>
                    </div>

                    @php $rolePermissions = $role->permissions->pluck('name')->toArray(); @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[50vh] overflow-y-auto custom-scrollbar pr-1">
                        @foreach($permissionGroups as $group => $permissions)
                        <div class="rounded-xl border border-stone-200 bg-stone-50/50 overflow-hidden">
                            <div class="px-3.5 py-2 bg-stone-100 border-b border-stone-200">
                                <h4 class="text-[11px] font-bold text-stone-600 uppercase tracking-wider">{{ $group }}</h4>
                            </div>
                            <div class="px-3.5 py-2.5 space-y-1.5">
                                @foreach($permissions as $perm => $label)
                                <label class="flex items-start gap-3 cursor-pointer group py-1 relative">
                                    <div class="relative flex items-center shrink-0 mt-0.5">
                                        <input type="checkbox" name="permissions[]" value="{{ $perm }}" {{ in_array($perm, $rolePermissions) ? 'checked' : '' }} class="peer sr-only">
                                        <div class="w-9 h-5 bg-stone-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#043d2e]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#043d2e] shadow-inner transition-colors"></div>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-[12px] font-bold text-stone-700 group-hover:text-[#043d2e] transition-colors">{{ $label }}</span>
                                        <p class="text-[10px] text-stone-400 font-mono leading-tight mt-0.5">{{ $perm }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <x-slot name="footer">
                    <button type="button" @click="$dispatch('close-modal', 'modal-edit-role-{{ $role->id }}')" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batal</button>
                    <button type="submit" form="form-edit-role-{{ $role->id }}" class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95">Simpan Perubahan</button>
                </x-slot>
            </form>
        </x-modal>
        @endif

        {{-- Info Modals --}}
        <x-modal name="modal-info-users-{{ $role->id }}" title="Pengguna dengan Role: {{ PermissionRegistry::roleLabel($role->name) }}" maxWidth="md">
            <div class="px-1 py-2">
                @if($role->users->count() > 0)
                    <div class="max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar space-y-2">
                        @foreach($role->users as $u)
                        <div class="flex items-center gap-3 p-3 bg-stone-50 border border-stone-200 rounded-xl">
                            <div class="w-10 h-10 rounded-full bg-[#043d2e]/10 text-[#043d2e] flex items-center justify-center font-bold text-[15px] shrink-0 border border-[#043d2e]/20">{{ strtoupper(substr($u->nama, 0, 1)) }}</div>
                            <div class="overflow-hidden">
                                <h4 class="text-[13px] font-bold text-stone-800 truncate">{{ $u->nama }}</h4>
                                <p class="text-[11px] text-stone-500 font-mono truncate">{{ $u->username }} {{ $u->nip ? '| '.$u->nip : '' }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-stone-500 text-sm">Belum ada pengguna dengan role ini.</div>
                @endif
            </div>
            <x-slot name="footer">
                <button type="button" @click="$dispatch('close-modal', 'modal-info-users-{{ $role->id }}')" class="px-5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 text-sm font-bold rounded-xl transition-colors w-full">Tutup</button>
            </x-slot>
        </x-modal>

        <x-modal name="modal-info-permissions-{{ $role->id }}" title="Permission Aktif: {{ PermissionRegistry::roleLabel($role->name) }}" maxWidth="lg">
            <div class="px-1 py-2">
                @if($role->permissions->count() > 0)
                    <div class="max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar flex flex-wrap gap-2">
                        @foreach($role->permissions->pluck('name') as $p)
                        <span class="inline-flex px-2.5 py-1.5 bg-[#043d2e]/5 text-[#043d2e] border border-[#043d2e]/20 rounded-lg text-[11px] font-bold">{{ \App\Services\PermissionRegistry::permissionLabel($p) }}</span>
                        @endforeach
                    </div>
                @else
                    <div class="py-8 text-center text-stone-500 text-sm">Role ini tidak memiliki permission aktif.</div>
                @endif
            </div>
            <x-slot name="footer">
                <button type="button" @click="$dispatch('close-modal', 'modal-info-permissions-{{ $role->id }}')" class="px-5 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 text-sm font-bold rounded-xl transition-colors w-full">Tutup</button>
            </x-slot>
        </x-modal>
        @endforeach
    </div>

</div>

{{-- Modal Tambah Role --}}
@can('role.create')
<x-modal name="modal-tambah-role" title="Tambah Role Baru" subtitle="Buat role baru dan atur permission-nya" maxWidth="3xl">
    <form id="form-tambah-role" action="{{ route('roles.store') }}" method="POST" class="contents">
        @csrf

        {{-- Role Name --}}
        <div class="mb-5">
            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-1.5">Nama Role</label>
            <input type="text" name="name" required value="{{ old('name') }}" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-[#043d2e]/20 focus:border-[#043d2e] text-sm text-stone-700 bg-stone-50 outline-none transition-all font-mono" placeholder="contoh: bendahara">
            <p class="text-[11px] text-stone-400 mt-1">Gunakan huruf kecil dan underscore. Contoh: <span class="font-mono">sekretaris_keuangan</span></p>
            @error('name') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
        </div>

        {{-- Permission Matrix --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider">Permissions</label>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleAllCheckboxes('form-tambah-role', true)" class="text-[11px] font-bold text-[#043d2e] hover:underline">Pilih Semua</button>
                    <span class="text-stone-300">|</span>
                    <button type="button" onclick="toggleAllCheckboxes('form-tambah-role', false)" class="text-[11px] font-bold text-red-500 hover:underline">Hapus Semua</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[50vh] overflow-y-auto custom-scrollbar pr-1">
                @foreach($permissionGroups as $group => $permissions)
                <div class="rounded-xl border border-stone-200 bg-stone-50/50 overflow-hidden">
                    <div class="px-3.5 py-2 bg-stone-100 border-b border-stone-200">
                        <h4 class="text-[11px] font-bold text-stone-600 uppercase tracking-wider">{{ $group }}</h4>
                    </div>
                    <div class="px-3.5 py-2.5 space-y-1.5">
                        @foreach($permissions as $perm => $label)
                        <label class="flex items-start gap-3 cursor-pointer group py-1 relative">
                            <div class="relative flex items-center shrink-0 mt-0.5">
                                <input type="checkbox" name="permissions[]" value="{{ $perm }}" class="peer sr-only">
                                <div class="w-9 h-5 bg-stone-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#043d2e]/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#043d2e] shadow-inner transition-colors"></div>
                            </div>
                            <div class="flex-1">
                                <span class="text-[12px] font-bold text-stone-700 group-hover:text-[#043d2e] transition-colors">{{ $label }}</span>
                                <p class="text-[10px] text-stone-400 font-mono leading-tight mt-0.5">{{ $perm }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <x-slot name="footer">
            <button type="button" @click="$dispatch('close-modal', 'modal-tambah-role')" class="px-4 py-2.5 text-sm font-bold text-stone-500 hover:text-stone-800 hover:bg-stone-200/50 rounded-xl transition-all">Batal</button>
            <button type="submit" form="form-tambah-role" class="px-6 py-2.5 bg-[#043d2e] hover:bg-[#043d2e]/90 text-white text-sm font-bold rounded-xl shadow-sm transition-all active:scale-95">Simpan Role</button>
        </x-slot>
    </form>
</x-modal>
@endcan

@push('scripts')
<script>
    function toggleAllCheckboxes(formId, checked) {
        const form = document.getElementById(formId);
        if (!form) return;
        form.querySelectorAll('input[type="checkbox"][name="permissions[]"]').forEach(cb => cb.checked = checked);
    }
</script>
@endpush
@endsection
