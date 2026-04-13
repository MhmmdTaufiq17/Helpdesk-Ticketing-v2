@extends('layouts.admin.app')

@section('title', 'Edit Profile')
@section('breadcrumb', 'Edit Profile')

@section('content')
    <div class="max-w-2xl mx-auto">
        {{-- Session Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
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

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Edit Profile</h2>
                    <p class="text-sm text-gray-500 mt-1">Ubah informasi akun Anda</p>
                </div>
                <a href="{{ route('admin.profile') }}" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    ← Kembali
                </a>
            </div>

            <div class="p-6">
                {{-- Form Update Nama --}}
                <form method="POST" action="{{ route('admin.profile.update-name') }}"
                    class="mb-8 pb-6 border-b border-gray-100">
                    @csrf
                    @method('PUT')
                    <h3 class="text-md font-semibold text-gray-800 mb-4">Ubah Nama</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#5b5ef4] focus:ring-2 focus:ring-indigo-100 outline-none transition-all">
                        @error('name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-[#5b5ef4] hover:bg-indigo-600 transition-all shadow-sm">
                        Simpan Nama
                    </button>
                </form>

                {{-- Form Request Change Email --}}
                <form method="POST" action="{{ route('admin.profile.email-change') }}" id="emailChangeForm">
                    @csrf
                    <h3 class="text-md font-semibold text-gray-800 mb-4">Ubah Email</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Saat Ini</label>
                        <input type="email" value="{{ $user->email }}" disabled
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm bg-gray-50 text-gray-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Baru</label>
                        <input type="email" name="email" id="newEmailInput" value="{{ old('email') }}"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-[#5b5ef4] focus:ring-2 focus:ring-indigo-100 outline-none transition-all"
                            placeholder="Masukkan email baru" required
                            {{ $user->hasPendingEmailChange() ? 'readonly' : '' }}>
                        @error('email')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Countdown Timer --}}
                    <div id="countdownContainer" class="hidden mb-4">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-700">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Link verifikasi telah dikirim ke email Anda saat ini.</span>
                            </div>
                            <div class="mt-2 text-center">
                                <span class="font-mono text-lg font-bold" id="timerDisplay">30:00</span>
                                <p class="text-xs mt-1">Token akan kadaluarsa dalam waktu di atas. Form akan otomatis
                                    direset setelah kadaluarsa.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 mb-4 text-xs text-blue-700"
                        id="infoMessage">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Link verifikasi akan dikirim ke email Anda saat ini ({{ $user->email }}). Klik link tersebut untuk
                        mengkonfirmasi perubahan email.
                    </div>

                    <button type="submit" id="submitBtn"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Kirim Link Verifikasi
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let hasSubmitted = false;

            @if ($user->hasPendingEmailChange())
                let expiresAt = new Date('{{ $user->email_change_token_expires_at }}').getTime();
                let timerInterval = null;

                function startCountdown() {
                    const newEmailInput = document.getElementById('newEmailInput');
                    const submitBtn = document.getElementById('submitBtn');
                    const countdownContainer = document.getElementById('countdownContainer');
                    const infoMessage = document.getElementById('infoMessage');
                    const timerDisplay = document.getElementById('timerDisplay');

                    // Gunakan readonly, bukan disabled
                    newEmailInput.readOnly = true;
                    newEmailInput.classList.add('bg-gray-50');

                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    countdownContainer.classList.remove('hidden');
                    infoMessage.classList.add('hidden');

                    timerInterval = setInterval(function() {
                        let now = new Date().getTime();
                        let distance = expiresAt - now;

                        if (distance <= 0) {
                            clearInterval(timerInterval);

                            // Reset: readonly -> false, kosongkan value
                            newEmailInput.readOnly = false;
                            newEmailInput.classList.remove('bg-gray-50');
                            newEmailInput.value = '';

                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                            countdownContainer.classList.add('hidden');
                            infoMessage.classList.remove('hidden');
                            infoMessage.innerHTML = `
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Token verifikasi telah kadaluarsa. Silakan kirim ulang permintaan perubahan email.
                            `;
                            infoMessage.classList.remove('bg-blue-50', 'border-blue-100', 'text-blue-700');
                            infoMessage.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
                        } else {
                            let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            timerDisplay.textContent =
                                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                        }
                    }, 1000);
                }

                startCountdown();
            @endif

            const form = document.getElementById('emailChangeForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const newEmailInput = document.getElementById('newEmailInput');
                    const submitBtn = document.getElementById('submitBtn');
                    const emailValue = newEmailInput ? newEmailInput.value.trim() : '';

                    if (hasSubmitted) {
                        e.preventDefault();
                        return false;
                    }

                    if (newEmailInput && newEmailInput.readOnly) {
                        e.preventDefault();
                        alert('Masih ada permintaan perubahan email yang pending. Tunggu hingga kadaluarsa.');
                        return false;
                    }

                    if (!emailValue) {
                        e.preventDefault();
                        alert('Masukkan email baru terlebih dahulu.');
                        return false;
                    }

                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(emailValue)) {
                        e.preventDefault();
                        alert('Masukkan format email yang valid (contoh: nama@domain.com).');
                        return false;
                    }

                    hasSubmitted = true;

                    // Gunakan readonly setelah submit
                    newEmailInput.readOnly = true;
                    newEmailInput.classList.add('bg-gray-50');

                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    submitBtn.innerHTML = `
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mengirim...
                    `;
                });
            }
        </script>
    @endpush
@endsection
