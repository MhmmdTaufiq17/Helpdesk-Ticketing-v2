@extends('layouts.admin.app')

@section('title', 'Statistik & Laporan')
@section('breadcrumb', 'Statistik')

@section('content')
    <div class="space-y-6">
        {{-- Statistik Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Total Tiket</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalTickets }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Total Admin</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalAdmins }}</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Rata-rata Respon</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $avgResponseTime }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider">Tiket Selesai</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $statusStats['closed'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Status & Prioritas --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Status Ringkasan --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Status Tiket</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 bg-blue-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Open</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-500 h-2 rounded-full"
                                    style="width: {{ $totalTickets > 0 ? ($statusStats['open'] / $totalTickets) * 100 : 0 }}%">
                                </div>
                            </div>
                            <span
                                class="text-sm font-semibold text-gray-700 w-12 text-right">{{ $statusStats['open'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 bg-yellow-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">In Progress</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-yellow-500 h-2 rounded-full"
                                    style="width: {{ $totalTickets > 0 ? ($statusStats['in_progress'] / $totalTickets) * 100 : 0 }}%">
                                </div>
                            </div>
                            <span
                                class="text-sm font-semibold text-gray-700 w-12 text-right">{{ $statusStats['in_progress'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Closed</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-green-500 h-2 rounded-full"
                                    style="width: {{ $totalTickets > 0 ? ($statusStats['closed'] / $totalTickets) * 100 : 0 }}%">
                                </div>
                            </div>
                            <span
                                class="text-sm font-semibold text-gray-700 w-12 text-right">{{ $statusStats['closed'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Prioritas Ringkasan --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Prioritas Tiket</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 bg-red-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Tinggi</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-red-500 h-2 rounded-full"
                                    style="width: {{ $totalTickets > 0 ? ($priorityStats['high'] / $totalTickets) * 100 : 0 }}%">
                                </div>
                            </div>
                            <span
                                class="text-sm font-semibold text-gray-700 w-12 text-right">{{ $priorityStats['high'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 bg-yellow-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Sedang</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-yellow-500 h-2 rounded-full"
                                    style="width: {{ $totalTickets > 0 ? ($priorityStats['medium'] / $totalTickets) * 100 : 0 }}%">
                                </div>
                            </div>
                            <span
                                class="text-sm font-semibold text-gray-700 w-12 text-right">{{ $priorityStats['medium'] }}</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-gray-600">Rendah</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-32 bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-green-500 h-2 rounded-full"
                                    style="width: {{ $totalTickets > 0 ? ($priorityStats['low'] / $totalTickets) * 100 : 0 }}%">
                                </div>
                            </div>
                            <span
                                class="text-sm font-semibold text-gray-700 w-12 text-right">{{ $priorityStats['low'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Chart Bulanan --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Tiket per Bulan</h3>
                <div style="height: 250px;">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            {{-- Chart Kategori --}}
            <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Tiket per Kategori</h3>
                <div style="height: 250px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Export Button --}}

        <div class="flex justify-end">
            @livewire('admin.export-modal')
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Chart (Line)
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($monthlyLabels) !!},
                    datasets: [{
                        data: {!! json_encode($monthlyTotals) !!},
                        borderColor: '#5b5ef4',
                        backgroundColor: 'rgba(91, 94, 244, 0.05)',
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#5b5ef4',
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.raw} tiket`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Category Chart (Pie)
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($categoryLabels) !!},
                    datasets: [{
                        data: {!! json_encode($categoryCounts) !!},
                        backgroundColor: ['#5b5ef4', '#3b82f6', '#06b6d4', '#10b981', '#f59e0b',
                            '#ef4444', '#8b5cf6', '#ec4899'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 10,
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
