<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Helpdesk</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/public-session.js'])
    @livewireStyles

    <style>
        .animate-content {
            animation: contentFadeUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
        }
    </style>

    @stack('styles')
</head>

<body
    class="font-['Sora',sans-serif] bg-slate-50 text-slate-950 antialiased min-h-screen flex flex-col m-0 overflow-x-hidden">

    {{-- {{ @slot }} --}}


    {{-- ── NAV ── --}}
    <nav
        class="fixed top-0 inset-x-0 z-[100] bg-white/80 backdrop-blur-md border-b border-slate-200 h-16 transition-all">
        <div class="max-w-[1200px] mx-auto px-6 h-full flex items-center gap-6">

            <a href="{{ route('user.home') }}" class="flex items-center gap-3 no-underline group shrink-0">
                <div
                    class="w-9 h-9 bg-[#5b5ef4] rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30 transition-transform group-hover:scale-105">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <span class="text-lg font-bold text-slate-900 tracking-tight">Helpdesk</span>
            </a>

            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('user.home') }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('user.home') ? 'bg-indigo-50 text-[#5b5ef4]' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Tiket
                </a>
            </div>

            {{-- Search Bar --}}
            <div class="hidden md:block flex-1 max-w-md ml-auto relative" id="desktopSearchWrap">
                <div
                    class="flex items-center bg-slate-100 border border-transparent rounded-xl px-3 group transition-all focus-within:bg-white focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-[#5b5ef4]">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="desktopSearchInput"
                        class="flex-1 border-none bg-transparent text-sm text-slate-900 py-2.5 px-2 outline-none placeholder:text-slate-400 min-w-0"
                        placeholder="Lacak kode tiket Anda..." autocomplete="off">
                    <button type="button" id="desktopSearchBtn"
                        class="bg-[#5b5ef4] text-white rounded-lg px-4 py-1.5 text-xs font-bold transition-all hover:bg-indigo-600 disabled:opacity-50 shrink-0">
                        Lacak
                    </button>
                </div>

                <div id="desktopSearchError"
                    class="hidden absolute top-full left-0 right-0 mt-2 bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 text-xs text-rose-600 shadow-xl z-[200] items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span id="desktopErrorMsg"></span>
                </div>
            </div>

            <button class="md:hidden flex items-center ml-auto p-2 text-slate-600 hover:bg-slate-100 rounded-xl"
                id="mobileMenuBtn">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </nav>

    {{-- ── MOBILE MENU ── --}}
    <div class="hidden fixed top-16 inset-x-0 bg-white border-b border-slate-200 px-6 py-6 z-[99] shadow-2xl animate-content"
        id="mobileMenu">

        {{-- FORM LACAK untuk MOBILE --}}
        <form id="mobileTrackForm" class="relative mb-6" method="POST" action="{{ route('user.tickets.track.do') }}">
            @csrf
            <div
                class="flex items-center bg-slate-100 rounded-xl px-3 border border-transparent focus-within:border-[#5b5ef4] focus-within:bg-white transition-all">
                <input type="text" name="ticket_code" id="mobileSearchInput"
                    class="flex-1 border-none bg-transparent text-sm text-slate-900 py-3 px-2 outline-none placeholder:text-slate-400"
                    placeholder="Masukkan kode tiket..." autocomplete="off" required>
                <button type="submit" id="mobileSearchBtn"
                    class="bg-[#5b5ef4] text-white rounded-lg px-5 py-2 text-sm font-bold hover:bg-indigo-600 transition-all disabled:opacity-50">
                    Lacak
                </button>
            </div>
            <div id="mobileSearchError"
                class="hidden mt-2 bg-rose-50 border border-rose-100 rounded-lg px-3 py-2 text-xs text-rose-600 items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span id="mobileErrorMsg"></span>
            </div>
        </form>

        <a href="{{ route('user.home') }}"
            class="flex items-center gap-3 px-4 py-4 rounded-xl text-sm font-bold no-underline transition-all {{ request()->routeIs('user.home') ? 'bg-indigo-50 text-[#5b5ef4]' : 'text-slate-600 hover:bg-slate-50' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat Tiket Baru
        </a>
    </div>

    {{-- ── MAIN CONTENT ── --}}
    <main class="pt-24 pb-12 flex-1 animate-content">
        @yield('content')
    </main>

    {{-- ── FOOTER ── --}}
    <footer class="bg-slate-950 text-slate-400 px-6 py-12">
        <div class="max-w-[1200px] mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <div class="flex items-center gap-2 text-white font-bold text-lg mb-2">
                    <div class="w-2 h-2 bg-[#5b5ef4] rounded-full"></div>
                    Helpdesk Portal
                </div>
                <p class="text-sm opacity-60">Sistem manajemen tiket bantuan pelanggan resmi.</p>
            </div>
            <div class="flex flex-col md:items-end gap-4">
                <div class="flex gap-6 text-sm">
                    <a href="#" class="hover:text-white transition-colors">Privasi</a>
                    <a href="#" class="hover:text-white transition-colors">Ketentuan</a>
                    <a href="#" class="hover:text-white transition-colors">FAQ</a>
                </div>
                <div class="text-xs opacity-50">
                    &copy; {{ date('Y') }} Helpdesk Ticketing System.
                </div>
            </div>
        </div>
    </footer>

    <script>
        var CSRF = '{{ csrf_token() }}';
        var TRACK_URL = '{{ route('user.tickets.track.do') }}';

        function triggerLoading() {
            if (window.NProgress) window.NProgress.start();
        }

        async function doSearch(code, btn, msgEl, errEl) {
            var trimmed = code.trim().toUpperCase();
            if (!trimmed) {
                msgEl.textContent = 'Masukkan kode tiket terlebih dahulu.';
                errEl.classList.remove('hidden');
                errEl.classList.add('flex');
                return;
            }

            btn.disabled = true;
            triggerLoading();
            errEl.classList.add('hidden');

            try {
                var res = await fetch(TRACK_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({
                        ticket_code: trimmed
                    }),
                });
                var data = await res.json();

                if (res.ok && data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    if (window.NProgress) window.NProgress.done();
                    btn.disabled = false;
                    msgEl.textContent = (data.errors && data.errors.ticket_code) ? data.errors.ticket_code[0] : (data
                        .message ?? 'Tiket tidak ditemukan.');
                    errEl.classList.remove('hidden');
                    errEl.classList.add('flex');
                }
            } catch (e) {
                if (window.NProgress) window.NProgress.done();
                btn.disabled = false;
                msgEl.textContent = 'Gagal terhubung ke server.';
                errEl.classList.remove('hidden');
            }
        }

        // Event Listeners
        document.getElementById('desktopSearchBtn')?.addEventListener('click', () => {
            doSearch(document.getElementById('desktopSearchInput').value, document.getElementById(
                'desktopSearchBtn'), document.getElementById('desktopErrorMsg'), document.getElementById(
                'desktopSearchError'));
        });

        const menuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        menuBtn?.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));

        // ── PERBAIKAN: Global Click untuk LINK saja (BUKAN BUTTON) ──
        document.addEventListener('click', (e) => {
            // Hanya proses jika yang diklik adalah LINK (a tag), BUKAN button
            const link = e.target.closest('a');

            // Jika yang diklik adalah button atau ada di dalam form, abaikan
            if (e.target.closest('button') || e.target.closest('form')) {
                return; // JANGAN trigger loading untuk button
            }

            if (link && link.href && !link.target && !link.href.includes('#') &&
                !link.href.startsWith('mailto') && !link.href.startsWith('tel')) {
                triggerLoading();
            }
        });

        // ── HANDLER UNTUK MOBILE FORM ──
        const mobileForm = document.getElementById('mobileTrackForm');
        const mobileInput = document.getElementById('mobileSearchInput');
        const mobileBtn = document.getElementById('mobileSearchBtn');
        const mobileError = document.getElementById('mobileSearchError');
        const mobileErrorMsg = document.getElementById('mobileErrorMsg');

        if (mobileForm) {
            mobileForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                e.stopPropagation(); // Mencegah event bubbling

                const code = mobileInput?.value || '';
                const trimmed = code.trim().toUpperCase();

                if (!trimmed) {
                    if (mobileErrorMsg) mobileErrorMsg.textContent = 'Masukkan kode tiket terlebih dahulu.';
                    if (mobileError) {
                        mobileError.classList.remove('hidden');
                        mobileError.classList.add('flex');
                    }
                    return;
                }

                // Disable button
                if (mobileBtn) {
                    mobileBtn.disabled = true;
                    mobileBtn.textContent = 'Memproses...';
                }

                triggerLoading();
                if (mobileError) mobileError.classList.add('hidden');

                try {
                    var res = await fetch(TRACK_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF
                        },
                        body: JSON.stringify({
                            ticket_code: trimmed
                        }),
                    });
                    var data = await res.json();

                    if (res.ok && data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        if (window.NProgress) window.NProgress.done();
                        if (mobileBtn) {
                            mobileBtn.disabled = false;
                            mobileBtn.textContent = 'Lacak';
                        }
                        const errorMsg = (data.errors && data.errors.ticket_code) ?
                            data.errors.ticket_code[0] : (data.message ?? 'Tiket tidak ditemukan.');
                        if (mobileErrorMsg) mobileErrorMsg.textContent = errorMsg;
                        if (mobileError) {
                            mobileError.classList.remove('hidden');
                            mobileError.classList.add('flex');
                        }
                    }
                } catch (error) {
                    if (window.NProgress) window.NProgress.done();
                    if (mobileBtn) {
                        mobileBtn.disabled = false;
                        mobileBtn.textContent = 'Lacak';
                    }
                    if (mobileErrorMsg) mobileErrorMsg.textContent = 'Gagal terhubung ke server.';
                    if (mobileError) {
                        mobileError.classList.remove('hidden');
                        mobileError.classList.add('flex');
                    }
                }
            });
        }

        document.addEventListener('submit', (e) => {
            // Abaikan form yang dihandle Livewire (wire:submit)
            if (e.target && (e.target.hasAttribute('wire:submit.prevent') || e.target.hasAttribute(
                    'wire:submit'))) {
                return;
            }
            // Jika form punya ID ticketForm, abaikan (jangan jalankan NProgress)
            if (e.target && e.target.id === 'ticketForm') {
                return;
            }

            if (!e.defaultPrevented) {
                if (window.NProgress) window.NProgress.start();
            }
        }, true); // Gunakan 'true' (capturing phase) jika stopPropagation di atas tidak mempan
    </script>

    @livewireScripts
    {{-- @vite('resources/js/app.js') --}}
    @stack('scripts')
</body>

</html>
