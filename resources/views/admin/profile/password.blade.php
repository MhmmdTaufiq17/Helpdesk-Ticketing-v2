@extends('layouts.admin.app')

@section('title', 'Ganti Password')
@section('breadcrumb', 'Ganti Password')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Ganti Password</h2>
                <p class="text-sm text-gray-500 mt-1">Ubah password akun Anda</p>
            </div>
            <a href="{{ route('admin.profile') }}"
                class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                ← Kembali
            </a>
        </div>

        <div class="p-6">
            {{-- Session Flash Messages --}}
            @if(session('info'))
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('info') }}
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            {{-- Countdown Timer untuk pending password reset --}}
            @if($user->password_reset_token && $user->password_reset_token_expires_at > now())
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Link verifikasi telah dikirim ke email Anda. Klik link tersebut untuk mengkonfirmasi perubahan password.</span>
                </div>
                <div class="mt-2 text-center">
                    <span class="font-mono text-lg font-bold" id="timerDisplay"></span>
                    <p class="text-xs mt-1">Token akan kadaluarsa dalam waktu di atas.</p>
                </div>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.profile.password-reset') }}" id="passwordResetForm">
                @csrf
                {{-- @method('PUT') --}} {{-- Ganti jadi POST, tanpa PUT --}}

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#5b5ef4] focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                            {{ $user->password_reset_token && $user->password_reset_token_expires_at > now() ? 'disabled' : '' }}>
                        @error('current_password')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                        <input type="password" name="password" id="password"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#5b5ef4] focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                            {{ $user->password_reset_token && $user->password_reset_token_expires_at > now() ? 'disabled' : '' }}>
                        @error('password')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#5b5ef4] focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                            {{ $user->password_reset_token && $user->password_reset_token_expires_at > now() ? 'disabled' : '' }}>
                    </div>
                </div>

                <div class="flex gap-3 mt-6 pt-4 border-t border-gray-100">
                    <button type="submit" id="submitBtn"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 transition-all shadow-sm"
                        {{ $user->password_reset_token && $user->password_reset_token_expires_at > now() ? 'disabled' : '' }}>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Kirim Link Verifikasi
                    </button>
                    <a href="{{ route('admin.profile') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    @if($user->password_reset_token && $user->password_reset_token_expires_at > now())
        let expiresAt = new Date('{{ $user->password_reset_token_expires_at }}').getTime();

        function startCountdown() {
            const timerDisplay = document.getElementById('timerDisplay');

            let interval = setInterval(function() {
                let now = new Date().getTime();
                let distance = expiresAt - now;

                if (distance <= 0) {
                    clearInterval(interval);
                    location.reload();
                } else {
                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                }
            }, 1000);
        }

        startCountdown();
    @endif
</script>
@endpush
@endsection
