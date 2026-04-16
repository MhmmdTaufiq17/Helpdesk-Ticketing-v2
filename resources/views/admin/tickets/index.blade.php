@extends('layouts.admin.app')

@section('title', 'Semua Tiket')
@section('breadcrumb', 'Semua Tiket')

@section('content')

    {{-- Page Header --}}
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Semua Tiket</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan pantau semua tiket masuk</p>
        </div>
    </div>

    {{-- Status Tabs --}}
    <div class="flex gap-1 bg-white border border-gray-200 rounded-xl p-1.5 mb-5 overflow-x-auto">
        <a href="{{ route('admin.tickets.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-all
              {{ !request('status') ? 'bg-indigo-500 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' }}">
            Semua
            <span
                class="text-xs font-bold px-2 py-0.5 rounded-full
                     {{ !request('status') ? 'bg-white/25 text-white' : 'bg-gray-100 text-gray-500' }}">
                {{ $counts['all'] }}
            </span>
        </a>
        <a href="{{ route('admin.tickets.index', array_merge(request()->except('status', 'page'), ['status' => 'open'])) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-all
              {{ request('status') === 'open' ? 'bg-indigo-500 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Open
            <span
                class="text-xs font-bold px-2 py-0.5 rounded-full
                     {{ request('status') === 'open' ? 'bg-white/25 text-white' : 'bg-red-100 text-red-600' }}">
                {{ $counts['open'] }}
            </span>
        </a>
        <a href="{{ route('admin.tickets.index', array_merge(request()->except('status', 'page'), ['status' => 'in_progress'])) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-all
              {{ request('status') === 'in_progress' ? 'bg-indigo-500 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            In Progress
            <span
                class="text-xs font-bold px-2 py-0.5 rounded-full
                     {{ request('status') === 'in_progress' ? 'bg-white/25 text-white' : 'bg-yellow-100 text-yellow-700' }}">
                {{ $counts['in_progress'] }}
            </span>
        </a>
        <a href="{{ route('admin.tickets.index', array_merge(request()->except('status', 'page'), ['status' => 'closed'])) }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold whitespace-nowrap transition-all
              {{ request('status') === 'closed' ? 'bg-indigo-500 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Closed
            <span
                class="text-xs font-bold px-2 py-0.5 rounded-full
                     {{ request('status') === 'closed' ? 'bg-white/25 text-white' : 'bg-green-100 text-green-700' }}">
                {{ $counts['closed'] }}
            </span>
        </a>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.tickets.index') }}" id="filterForm">
        @if (request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3 mb-4 flex items-center gap-3 flex-wrap">
            {{-- Search --}}
            <div
                class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-lg px-3 flex-1 min-w-48 focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100 focus-within:bg-white transition-all">
                <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="q" value="{{ request('q') }}"
                    placeholder="Cari kode, judul, nama, email…"
                    class="flex-1 border-none bg-transparent text-sm text-gray-800 py-2 outline-none placeholder-gray-400"
                    autocomplete="off">
            </div>

            {{-- Priority --}}
            <select name="priority" onchange="this.form.submit()"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-gray-50 outline-none cursor-pointer appearance-none pr-8 focus:border-indigo-400 focus:bg-white transition-all">
                <option value="">Semua Prioritas</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Tinggi</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Sedang</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Rendah</option>
            </select>

            {{-- Category --}}
            <select name="category" onchange="this.form.submit()"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-gray-50 outline-none cursor-pointer appearance-none pr-8 focus:border-indigo-400 focus:bg-white transition-all">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->category_name }}
                    </option>
                @endforeach
            </select>

            @if (request()->anyFilled(['q', 'priority', 'category']))
                <a href="{{ route('admin.tickets.index', request('status') ? ['status' => request('status')] : []) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all whitespace-nowrap">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Reset
                </a>
            @endif

            <button type="submit"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-indigo-500 hover:bg-indigo-600 transition-all whitespace-nowrap shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Cari
            </button>
        </div>
    </form>

    {{-- Active chips --}}
    @if (request()->anyFilled(['q', 'priority', 'category']))
        <div class="flex items-center gap-2 flex-wrap mb-4">
            <span class="text-xs text-gray-400">Filter aktif:</span>
            @if (request('q'))
                <span
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
                    "{{ request('q') }}"
                    <a href="{{ request()->fullUrlWithoutQuery(['q']) }}" class="opacity-60 hover:opacity-100 ml-0.5">✕</a>
                </span>
            @endif
            @if (request('priority'))
                <span
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
                    {{ ucfirst(request('priority')) }}
                    <a href="{{ request()->fullUrlWithoutQuery(['priority']) }}"
                        class="opacity-60 hover:opacity-100 ml-0.5">✕</a>
                </span>
            @endif
            @if (request('category'))
                @php $activeCat = $categories->firstWhere('id', request('category')); @endphp
                <span
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
                    {{ $activeCat?->category_name }}
                    <a href="{{ request()->fullUrlWithoutQuery(['category']) }}"
                        class="opacity-60 hover:opacity-100 ml-0.5">✕</a>
                </span>
            @endif
        </div>
    @endif

    {{-- Meta --}}
    <div class="flex items-center justify-between mb-3 text-sm text-gray-400">
        <span>Menampilkan <span
                class="font-semibold text-gray-700">{{ $tickets->firstItem() ?? 0 }}–{{ $tickets->lastItem() ?? 0 }}</span>
            dari <span class="font-semibold text-gray-700">{{ $tickets->total() }}</span> tiket</span>
        <span>Hal. {{ $tickets->currentPage() }} / {{ $tickets->lastPage() }}</span>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th
                            class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Kode</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Judul
                            / Pelapor</th>
                        <th
                            class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Kategori</th>
                        <th
                            class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Prioritas</th>
                        <th
                            class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Status</th>
                        <th
                            class="text-left px-4 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap">
                            Masuk</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="font-mono text-xs font-semibold text-indigo-500">{{ $ticket->ticket_code }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 max-w-xs">
                                <div class="font-medium text-gray-800 truncate" title="{{ $ticket->title }}">
                                    {{ $ticket->title }}</div>
                                <div class="text-xs text-gray-400 truncate mt-0.5">{{ $ticket->client_name }} ·
                                    {{ $ticket->client_email }}</div>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="text-xs text-gray-600">{{ $ticket->category?->category_name ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if ($ticket->priority === 'high')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>Tinggi
                                    </span>
                                @elseif($ticket->priority === 'medium')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-yellow-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 inline-block"></span>Sedang
                                    </span>
                                @elseif($ticket->priority === 'low')
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>Rendah
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                @if ($ticket->status === 'open')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Open</span>
                                @elseif($ticket->status === 'in_progress')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">In
                                        Progress</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Closed</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-400">
                                {{ $ticket->created_at->format('d M Y') }}<br>
                                <span class="text-gray-300">{{ $ticket->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <a href="{{ route('admin.tickets.show', $ticket->id) }}"
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 border border-gray-200 transition-all">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="text-center py-16">
                                    <div
                                        class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-gray-500 mb-1">Tidak ada tiket ditemukan</p>
                                    <p class="text-sm text-gray-400">Coba ubah filter atau kata kunci pencarian</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($tickets->hasPages())
            <div class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100 flex-wrap gap-3">
                <span class="text-sm text-gray-400">{{ $tickets->total() }} tiket total</span>
                <div class="flex gap-1">
                    @if ($tickets->onFirstPage())
                        <span
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 opacity-40 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $tickets->previousPageUrl() }}"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                    @endif

                    @foreach ($tickets->getUrlRange(max(1, $tickets->currentPage() - 2), min($tickets->lastPage(), $tickets->currentPage() + 2)) as $page => $url)
                        @if ($page == $tickets->currentPage())
                            <span
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-semibold bg-indigo-500 text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($tickets->hasMorePages())
                        <a href="{{ $tickets->nextPageUrl() }}"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-500 hover:bg-gray-100 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <span
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 opacity-40 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>

@endsection
