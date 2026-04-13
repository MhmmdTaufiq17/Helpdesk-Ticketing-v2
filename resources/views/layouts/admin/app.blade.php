    <!DOCTYPE html>
    <html lang="id" class="scroll-smooth">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Dashboard') — Admin Helpdesk</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Sora:wght@700;800&family=DM+Mono:wght@400;500&display=swap"
            rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f8fafc;
            }

            .sidebar-gradient {
                background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            }

            .sidebar-nav::-webkit-scrollbar {
                width: 4px;
            }

            .sidebar-nav::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 10px;
            }

            .animate-content {
                animation: contentFadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) both;
            }

            @keyframes contentFadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Hover effect untuk avatar */
            .avatar-hover {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .avatar-hover:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }
        </style>

        @stack('styles')
    </head>

    <body class="text-slate-900 m-0 min-h-screen antialiased overflow-x-hidden">
        @php
            $sidebarOpenCount = \App\Models\Ticket::where('status', 'open')->count();
        @endphp

        <div class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[190] transition-opacity"
            id="sidebarOverlay">
        </div>

        {{-- ── SIDEBAR ── --}}
        <aside
            class="fixed top-0 left-0 bottom-0 w-[260px] sidebar-gradient border-r border-white/5 flex flex-col z-[200] transition-all duration-300 lg:translate-x-0 -translate-x-full"
            id="sidebar">

            <div class="flex items-center gap-3 px-6 h-20 border-b border-white/5 shrink-0">
                <div
                    class="w-9 h-9 bg-[#5b5ef4] rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-['Sora'] font-bold text-white tracking-tight leading-none">Helpdesk</span>
                    <span class="text-[10px] font-medium text-indigo-300 tracking-wider uppercase mt-1 opacity-70">Admin
                        Portal</span>
                </div>
            </div>

            <nav class="sidebar-nav flex-1 overflow-y-auto px-4 py-6 space-y-8">
                <div>
                    <span class="text-[11px] font-bold text-slate-500 tracking-[1.5px] uppercase px-3">Menu Utama</span>
                    <div class="mt-4 space-y-1">
                        <a href="{{ route('admin.dashboard') }}"
                            class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-[#5b5ef4] text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span class="font-medium">Dashboard</span>
                        </a>
                    </div>
                </div>

                <div>
                    <span class="text-[11px] font-bold text-slate-500 tracking-[1.5px] uppercase px-3">Manajemen
                        Tiket</span>
                    <div class="mt-4 space-y-1">
                        <a href="{{ route('admin.tickets.index') }}"
                            class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.tickets.index') && !request()->has('status') ? 'bg-[#5b5ef4] text-white shadow-lg' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <span class="font-medium">Semua Tiket</span>
                            </div>
                            @if ($sidebarOpenCount > 0)
                                <span
                                    class="text-[11px] font-bold bg-rose-500 text-white px-2 py-0.5 rounded-lg">{{ $sidebarOpenCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.tickets.index', ['status' => 'open']) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request('status') === 'open' ? 'bg-[#5b5ef4] text-white shadow-lg' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium text-[13.5px]">Tiket Baru</span>
                        </a>
                        <a href="{{ route('admin.tickets.index', ['status' => 'in_progress']) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request('status') === 'in_progress' ? 'bg-[#5b5ef4] text-white shadow-lg' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="font-medium text-[13.5px]">Tiket Proses</span>
                        </a>
                        <a href="{{ route('admin.tickets.index', ['status' => 'closed']) }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request('status') === 'closed' ? 'bg-[#5b5ef4] text-white shadow-lg' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                            <svg class="w-5 h-5 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium text-[13.5px]">Tiket Selesai</span>
                        </a>
                    </div>
                </div>

                <div>
                    <span class="text-[11px] font-bold text-slate-500 tracking-[1.5px] uppercase px-3">Laporan &
                        Pengaturan</span>
                    <div class="mt-4 space-y-1">
                        @if (auth()->user() && auth()->user()->isSuperAdmin())
                            <a href="{{ route('admin.manajemen.index') }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.manajemen.*') ? 'bg-[#5b5ef4] text-white shadow-lg' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span class="font-medium">Manajemen Admin</span>
                            </a>
                        @endif


                        <a href="{{ route('admin.reports.index') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.reports.index') ? 'bg-[#5b5ef4] text-white shadow-lg' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span class="font-medium">Statistik</span>
                        </a>

                        {{-- Profile untuk SEMUA admin (bukan hanya Super Admin) --}}
                        <a href="{{ route('admin.profile') }}"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.profile') ? 'bg-[#5b5ef4] text-white shadow-lg' : 'text-slate-400 hover:bg-white/5 hover:text-slate-200' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="font-medium">Profile</span>
                        </a>
                    </div>
                </div>
            </nav>

            {{-- Sidebar Footer --}}
            <div class="p-4 mt-auto">
                <div class="bg-white/5 rounded-2xl p-4 border border-white/5">
                    <div class="flex items-center gap-3">
                        {{-- Avatar warna ungu fix --}}
                        <div
                            class="w-10 h-10 rounded-xl bg-[#5b5ef4] flex items-center justify-center text-white font-bold text-base shadow-inner">
                            {{ auth()->user() ? auth()->user()->getInitial() : 'A' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'Admin' }}
                            </p>
                            <p class="text-[11px] text-slate-500 truncate">
                                {{ auth()->user() && auth()->user()->isSuperAdmin() ? 'Super Admin' : 'Administrator' }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="text-slate-500 hover:text-rose-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ── MAIN WRAPPER ── --}}
        <div class="lg:ml-[260px] flex flex-col min-h-screen transition-all duration-300">

            <header
                class="sticky top-0 h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center px-8 gap-6 z-[100]">
                <button class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg" id="hamburgerBtn">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <nav class="hidden md:flex items-center gap-2 text-sm">
                    <span class="text-slate-400 font-medium">Portal Admin</span>
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-slate-900 font-semibold">@yield('breadcrumb', 'Dashboard')</span>
                </nav>

                <div class="flex-1 flex justify-end items-center gap-4">
                    <div class="relative hidden sm:block group">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="adminQuickSearch" placeholder="Cari kode tiket..."
                            class="pl-10 pr-4 py-2 bg-slate-100 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-[#5b5ef4] rounded-xl text-sm w-64 transition-all outline-none">
                    </div>

                    <button class="relative p-2.5 text-slate-500 hover:bg-slate-100 rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span
                            class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 rounded-full border-2 border-white {{ $sidebarOpenCount > 0 ? 'block' : 'hidden' }}"></span>
                    </button>
                </div>
            </header>

            <main class="p-8 flex-1">
                <div class="max-w-7xl mx-auto animate-content">
                    @if (session('success'))
                        <div
                            class="flex items-center gap-3 p-4 mb-6 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl">
                            <div
                                class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div
                            class="flex items-center gap-3 p-4 mb-6 bg-red-50 border border-red-100 text-red-700 rounded-2xl">
                            <div
                                class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center text-white shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>

        <script>
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const hamburger = document.getElementById('hamburgerBtn');

            function toggleSidebar() {
                if (sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }

            if (hamburger) hamburger.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', toggleSidebar);

            const qs = document.getElementById('adminQuickSearch');
            if (qs) {
                qs.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        const val = qs.value.trim().toUpperCase();
                        if (val) {
                            if (window.NProgress) window.NProgress.start();
                            window.location.href = '{{ route('admin.tickets.search') }}?q=' + encodeURIComponent(val);
                        }
                    }
                });
            }
        </script>

        @livewireScripts
        @stack('scripts')
    </body>

    </html>
