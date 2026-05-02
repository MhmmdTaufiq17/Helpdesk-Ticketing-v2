@extends('layouts.admin.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div class="flex-1 min-w-0">
            <h1 class="text-[22px] font-bold text-slate-900 tracking-[-0.4px] m-0 mb-1">Dashboard</h1>
            <p class="text-[13px] text-slate-500 m-0">Selamat datang, {{ auth()->user()->name }}. Berikut ringkasan tiket
                hari ini.</p>
        </div>

    </div>  

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div
            class="stat-card bg-white border border-slate-200 rounded-[14px] p-[18px_20px] flex flex-col gap-3 shadow-[0_1px_4px_rgba(0,0,0,.07)] transition-shadow hover:shadow-[0_4px_16px_rgba(0,0,0,.09)]">
            <div class="flex items-center justify-between">
                <span class="text-[12px] font-semibold text-slate-400 uppercase tracking-[0.5px]">Total Tiket</span>
                <div class="w-9 h-9 rounded-[10px] flex items-center justify-center bg-blue-50 text-blue-500">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
            </div>
            <div class="text-[28px] font-extrabold text-slate-900 tracking-[-1px] leading-none">{{ $stats['total'] }}</div>
            <div class="text-[12px] text-slate-500 flex items-center gap-1">Semua tiket masuk</div>
        </div>

        <div
            class="stat-card bg-white border border-slate-200 rounded-[14px] p-[18px_20px] flex flex-col gap-3 shadow-[0_1px_4px_rgba(0,0,0,.07)] transition-shadow hover:shadow-[0_4px_16px_rgba(0,0,0,.09)]">
            <div class="flex items-center justify-between">
                <span class="text-[12px] font-semibold text-slate-400 uppercase tracking-[0.5px]">Open</span>
                <div class="w-9 h-9 rounded-[10px] flex items-center justify-center bg-red-50 text-red-500">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-[28px] font-extrabold text-slate-900 tracking-[-1px] leading-none">{{ $stats['open'] }}</div>
            <div class="text-[12px] text-slate-500 flex items-center gap-1">Menunggu penanganan</div>
        </div>

        <div
            class="stat-card bg-white border border-slate-200 rounded-[14px] p-[18px_20px] flex flex-col gap-3 shadow-[0_1px_4px_rgba(0,0,0,.07)] transition-shadow hover:shadow-[0_4px_16px_rgba(0,0,0,.09)]">
            <div class="flex items-center justify-between">
                <span class="text-[12px] font-semibold text-slate-400 uppercase tracking-[0.5px]">In Progress</span>
                <div class="w-9 h-9 rounded-[10px] flex items-center justify-center bg-amber-50 text-amber-500">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
            </div>
            <div class="text-[28px] font-extrabold text-slate-900 tracking-[-1px] leading-none">{{ $stats['in_progress'] }}
            </div>
            <div class="text-[12px] text-slate-500 flex items-center gap-1">Sedang diproses</div>
        </div>

        <div
            class="stat-card bg-white border border-slate-200 rounded-[14px] p-[18px_20px] flex flex-col gap-3 shadow-[0_1px_4px_rgba(0,0,0,.07)] transition-shadow hover:shadow-[0_4px_16px_rgba(0,0,0,.09)]">
            <div class="flex items-center justify-between">
                <span class="text-[12px] font-semibold text-slate-400 uppercase tracking-[0.5px]">Closed</span>
                <div class="w-9 h-9 rounded-[10px] flex items-center justify-center bg-green-50 text-green-600">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="text-[28px] font-extrabold text-slate-900 tracking-[-1px] leading-none">{{ $stats['closed'] }}</div>
            <div class="text-[12px] text-slate-500 flex items-center gap-1">Berhasil diselesaikan</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">

        {{-- Bar chart: tiket per bulan --}}
        <div class="bg-white border border-slate-200 rounded-[14px] shadow-[0_1px_4px_rgba(0,0,0,.07)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                <div>
                    <p class="text-[14px] font-bold text-slate-900 m-0">Tiket Masuk per Bulan</p>
                    <p class="text-[12px] text-slate-500 m-0 mt-1">Januari – Desember {{ now()->year }}</p>
                </div>
            </div>
            <div class="p-5">
                <canvas id="chartMonthly" height="200"></canvas>
            </div>
        </div>

        {{-- Bar chart: top 10 kategori --}}
        <div class="bg-white border border-slate-200 rounded-[14px] shadow-[0_1px_4px_rgba(0,0,0,.07)] overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                <div>
                    <p class="text-[14px] font-bold text-slate-900 m-0">Top 10 Kategori</p>
                    <p class="text-[12px] text-slate-500 m-0 mt-1">Kategori dengan tiket terbanyak</p>
                </div>
            </div>
            <div class="p-5">
                <canvas id="chartCategory" height="200"></canvas>
            </div>
        </div>

    </div>

    {{-- Recent Tickets --}}
    <div class="bg-white border border-slate-200 rounded-[14px] shadow-[0_1px_4px_rgba(0,0,0,.07)] overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
            <div>
                <p class="text-[14px] font-bold text-slate-900 m-0">Tiket Terbaru</p>
                <p class="text-[12px] text-slate-500 m-0 mt-1">10 tiket yang baru masuk</p>
            </div>
            <a href="{{ route('admin.tickets.index') }}"
                class="inline-flex items-center justify-center gap-1.5 px-[11px] py-[5px] bg-white text-slate-700 border-[1.5px] border-slate-200 rounded-[9px] font-['Sora',sans-serif] text-[12px] font-semibold no-underline transition-all hover:bg-slate-50 hover:border-slate-300 hover:text-slate-900">Lihat
                Semua</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-[13px]">
                <thead>
                    <tr>
                        <th
                            class="text-left px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-[0.6px] border-b border-slate-200 whitespace-nowrap">
                            Kode Tiket</th>
                        <th
                            class="text-left px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-[0.6px] border-b border-slate-200 whitespace-nowrap">
                            Judul</th>
                        <th
                            class="text-left px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-[0.6px] border-b border-slate-200 whitespace-nowrap">
                            Kategori</th>
                        <th
                            class="text-left px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-[0.6px] border-b border-slate-200 whitespace-nowrap">
                            Prioritas</th>
                        <th
                            class="text-left px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-[0.6px] border-b border-slate-200 whitespace-nowrap">
                            Status</th>
                        <th
                            class="text-left px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-[0.6px] border-b border-slate-200 whitespace-nowrap">
                            Masuk</th>
                        <th
                            class="text-left px-4 py-2.5 text-[11px] font-semibold text-slate-500 uppercase tracking-[0.6px] border-b border-slate-200 whitespace-nowrap">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTickets as $ticket)
                        <tr class="transition-colors hover:bg-slate-50">
                            <td class="px-4 py-3.5 border-b border-slate-200 align-middle">
                                <span
                                    class="font-['DM_Mono',monospace] text-[12px] text-indigo-600">{{ $ticket->ticket_code }}</span>
                            </td>
                            <td class="px-4 py-3.5 border-b border-slate-200 align-middle max-w-[220px]">
                                <span
                                    class="block overflow-hidden text-ellipsis whitespace-nowrap text-slate-900 font-medium">{{ $ticket->title }}</span>
                                <span class="text-[11.5px] text-slate-500">{{ $ticket->client_name }}</span>
                            </td>
                            <td class="px-4 py-3.5 border-b border-slate-200 align-middle">
                                <span
                                    class="text-[12.5px] text-slate-700">{{ $ticket->category->category_name ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-3.5 border-b border-slate-200 align-middle">
                                @if ($ticket->priority === 'high')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-red-100 text-red-600 rounded-full text-[11.5px] font-semibold">Tinggi</span>
                                @elseif($ticket->priority === 'medium')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-[11.5px] font-semibold">Sedang</span>
                                @elseif($ticket->priority === 'low')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-[11.5px] font-semibold">Rendah</span>
                                @else
                                    <span class="text-[12px] text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 border-b border-slate-200 align-middle">
                                @if ($ticket->status === 'open')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-blue-100 text-blue-600 rounded-full text-[11.5px] font-semibold">Open</span>
                                @elseif($ticket->status === 'in_progress')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-[11.5px] font-semibold">In
                                        Progress</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-[11.5px] font-semibold">Closed</span>
                                @endif
                            </td>
                            <td
                                class="px-4 py-3.5 border-b border-slate-200 align-middle text-[12px] text-slate-500 whitespace-nowrap">
                                {{ $ticket->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3.5 border-b border-slate-200 align-middle text-right">
                                <a href="{{ route('admin.tickets.show', $ticket) }}"
                                    class="inline-flex items-center justify-center gap-1.5 px-[11px] py-[5px] bg-white text-slate-700 border-[1.5px] border-slate-200 rounded-[9px] font-['Sora',sans-serif] text-[12px] font-semibold no-underline transition-all hover:bg-slate-50 hover:border-slate-300 hover:text-slate-900">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 px-4 text-slate-500">
                                Belum ada tiket masuk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // ── Tiket per bulan ──
        var monthlyCtx = document.getElementById('chartMonthly').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                        label: 'Tiket Masuk',
                        data: @json($chartMonthly['incoming']),
                        backgroundColor: 'rgba(91,94,244,.75)',
                        borderRadius: 6,
                        borderSkipped: false
                    },
                    {
                        label: 'Tiket Closed',
                        data: @json($chartMonthly['closed']),
                        backgroundColor: 'rgba(34,197,94,.65)',
                        borderRadius: 6,
                        borderSkipped: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                family: 'Sora',
                                size: 12
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Sora',
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                family: 'Sora',
                                size: 11
                            },
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // ── Top 10 kategori ──
        var catCtx = document.getElementById('chartCategory').getContext('2d');
        new Chart(catCtx, {
            type: 'bar',
            data: {
                labels: @json($chartCategory['labels']),
                datasets: [{
                    label: 'Jumlah Tiket',
                    data: @json($chartCategory['values']),
                    backgroundColor: [
                        'rgba(91,94,244,.8)', 'rgba(59,130,246,.8)', 'rgba(34,197,94,.8)',
                        'rgba(245,158,11,.8)', 'rgba(239,68,68,.8)', 'rgba(168,85,247,.8)',
                        'rgba(20,184,166,.8)', 'rgba(249,115,22,.8)', 'rgba(236,72,153,.8)',
                        'rgba(99,102,241,.8)'
                    ],
                    borderRadius: 5,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                family: 'Sora',
                                size: 11
                            },
                            stepSize: 1
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: 'Sora',
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
