@extends('layouts.admin.app')

@section('title', 'Manajemen Admin')
@section('breadcrumb', 'Manajemen Admin')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center flex-wrap gap-4">
        <div>
            <h2 class="text-lg font-semibold text-slate-800">Daftar Administrator</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola semua akun administrator di sini</p>
        </div>
        <a href="{{ route('admin.manajemen.create') }}"
           class="bg-[#5b5ef4] hover:bg-[#4a4dd4] text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Admin Baru
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Dibuat</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($admins as $admin)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            {{-- Avatar --}}
                            @if($admin->avatar && Storage::disk('public')->exists('avatars/' . $admin->avatar))
                                <img src="{{ Storage::url('avatars/' . $admin->avatar) }}"
                                     class="w-10 h-10 rounded-xl object-cover shadow-sm"
                                     alt="{{ $admin->name }}">
                            @else
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#5b5ef4] to-[#7c7ff8] flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                    {{ $admin->getInitial() }}
                                </div>
                            @endif
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-slate-800">{{ $admin->name }}</span>
                                    @if($admin->id === auth()->id())
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 text-slate-500">Anda</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-slate-600 text-sm">{{ $admin->email }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($admin->isSuperAdmin())
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gradient-to-r from-amber-100 to-amber-50 text-amber-800 rounded-lg text-xs font-semibold border border-amber-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                                Super Admin
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-semibold border border-blue-100">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Admin
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-slate-700">{{ $admin->created_at->translatedFormat('d M Y') }}</div>
                        <div class="text-xs text-slate-400">{{ $admin->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            {{-- Tombol Lihat Profile --}}
                            <a href="{{ route('admin.manajemen.show', $admin->id) }}"
                               class="text-slate-400 hover:text-[#5b5ef4] transition-colors p-2 rounded-lg hover:bg-slate-100"
                               title="Lihat Profile">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>

                            {{-- Tombol Hapus (hanya untuk admin lain, bukan diri sendiri, dan super admin) --}}
                            @if($admin->id !== auth()->id() && auth()->user()->isSuperAdmin())
                            <form action="{{ route('admin.manajemen.destroy', $admin->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus admin {{ $admin->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-600 transition-colors p-2 rounded-lg hover:bg-red-50" title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="text-slate-500">Belum ada data admin</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($admins->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
        {{ $admins->links() }}
    </div>
    @endif
</div>
@endsection
