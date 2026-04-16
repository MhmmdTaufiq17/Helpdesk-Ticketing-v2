@extends('layouts.admin.app')

@section('title', 'Profile ' . $user->name)
@section('breadcrumb', 'Profile')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Profile {{ $user->name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Informasi akun administrator</p>
                </div>
                <div class="flex gap-2">
                    @if (auth()->user()->isSuperAdmin() && auth()->id() !== $user->id)
                        <a href="{{ route('admin.manajemen.index') }}"
                            class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                            ← Kembali ke Daftar Admin
                        </a>
                    @else
                        <a href="{{ route('admin.dashboard') }}"
                            class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                            ← Kembali
                        </a>
                    @endif
                </div>
            </div>

            <div class="p-6">
                {{-- Avatar --}}
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
                    @if ($user->avatar && Storage::disk('public')->exists('avatars/' . $user->avatar))
                        <img src="{{ Storage::url('avatars/' . $user->avatar) }}"
                            class="w-24 h-24 rounded-2xl object-cover shadow-lg" alt="{{ $user->name }}">
                    @else
                        <div
                            class="w-24 h-24 rounded-2xl bg-[#5b5ef4] flex items-center justify-center text-white font-bold text-3xl shadow-lg">
                            {{ $user->getInitial() }}
                        </div>
                    @endif
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $user->getRoleLabelAttribute() }}</p>
                        <p class="text-xs text-gray-400 mt-2">Member sejak
                            {{ $user->created_at->translatedFormat('d F Y') }}</p>
                        @if ($user->id === auth()->id())
                            <span class="inline-block mt-2 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Anda
                                sendiri</span>
                        @endif
                    </div>
                </div>

                {{-- Informasi Profile --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">Nama Lengkap</div>
                        <div class="text-base font-semibold text-gray-800">{{ $user->name }}</div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">Alamat Email</div>
                        <div class="text-base font-semibold text-gray-800">{{ $user->email }}</div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">Role</div>
                        <div class="text-base font-semibold text-gray-800">{{ $user->getRoleLabelAttribute() }}</div>
                    </div>

                    {{-- <div class="bg-gray-50 rounded-xl p-4">
                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">Terakhir Aktif</div>
                        <div class="text-base font-semibold text-gray-800">
                            {{ $user->last_seen_at ? $user->last_seen_at->translatedFormat('d F Y, H:i') : 'Belum pernah login' }}
                        </div>
                    </div> --}}

                    {{-- <div class="bg-gray-50 rounded-xl p-4">
                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">Status Online</div>
                        <div class="flex items-center gap-2 mt-1">
                            @if ($user->is_online)
                                <div class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></div>
                                <span class="text-sm font-medium text-green-600">Online</span>
                            @else
                                <div class="w-2.5 h-2.5 bg-gray-400 rounded-full"></div>
                                <span class="text-sm font-medium text-gray-500">Offline</span>
                            @endif
                        </div>
                    </div> --}}

                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="text-xs text-gray-400 uppercase tracking-wider mb-1">Tanggal Dibuat</div>
                        <div class="text-base font-semibold text-gray-800">
                            {{ $user->created_at->translatedFormat('d F Y, H:i') }}</div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-8 pt-6 border-t border-gray-100 flex gap-3">
                    {{-- Tombol Edit Profile (HANYA UNTUK DIRI SENDIRI) --}}
                    @if (auth()->id() === $user->id)
                        <a href="{{ route('admin.profile.edit') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-[#5b5ef4] hover:bg-indigo-600 transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            Edit Profile
                        </a>

                        {{-- Tombol Ubah Password (HANYA UNTUK DIRI SENDIRI) --}}
                        <a href="{{ route('admin.profile.password.form') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Ubah Password
                        </a>
                    @endif

                    {{-- Tombol Kembali --}}
                    @if (auth()->user()->isSuperAdmin() && auth()->id() !== $user->id)
                        <a href="{{ route('admin.manajemen.index') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all">
                            Kembali ke Daftar
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
