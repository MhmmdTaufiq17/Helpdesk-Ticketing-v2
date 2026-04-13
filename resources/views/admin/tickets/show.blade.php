    @extends('layouts.admin.app')

    @section('title', 'Detail Tiket #' . $ticket->ticket_code)
    @section('breadcrumb', 'Detail Tiket')

    @section('content')

        @php
            $sk = strtolower($ticket->status);
            $slbl = ['open' => 'Open', 'in_progress' => 'In Progress', 'closed' => 'Closed'][$sk] ?? ucfirst($sk);
            $pri = ['high' => 'Tinggi', 'medium' => 'Sedang', 'low' => 'Rendah'][$ticket->priority] ?? '—';
            $icons = [
                'open' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'in_progress' => 'M13 10V3L4 14h7v7l9-11h-7z',
                'closed' => 'M5 13l4 4L19 7',
            ];
            $notes = [
                'open' => 'Tiket masuk dan menunggu penanganan',
                'in_progress' => 'Tim sedang menganalisis dan mengerjakan solusi',
                'closed' => 'Tiket telah diselesaikan dan ditutup',
            ];
        @endphp

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
            <a href="{{ route('admin.tickets.index') }}" class="text-indigo-500 font-medium hover:underline">Semua Tiket</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="font-semibold text-gray-700 font-mono">{{ $ticket->ticket_code }}</span>
        </div>

        {{-- Hero / Header Ticket --}}
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm mb-6">
            <div class="flex items-center justify-between gap-4 flex-wrap px-6 py-5 border-b border-gray-100">
                <div>
                    <div class="font-mono text-xl font-bold text-indigo-500 tracking-tight">{{ $ticket->ticket_code }}</div>
                    <div class="text-sm text-gray-500 mt-1">{{ $ticket->title }}</div>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    @livewire('admin.ticket-status-badge', ['ticketId' => $ticket->id], key($ticket->id))
                    <span class="text-xs text-gray-400">Diperbarui {{ $ticket->updated_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-gray-100">
                <div class="px-5 py-4">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Pelapor</div>
                    <div class="text-sm font-semibold text-gray-800">{{ $ticket->client_name }}</div>
                </div>
                <div class="px-5 py-4">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Prioritas</div>
                    <div class="text-sm font-semibold text-gray-800">{{ $pri }}</div>
                </div>
                <div class="px-5 py-4">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Kategori</div>
                    <div class="text-sm font-semibold text-gray-800">{{ $ticket->category?->category_name ?? '—' }}</div>
                </div>
                <div class="px-5 py-4">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Dibuat</div>
                    <div class="text-sm font-semibold font-mono text-gray-800">{{ $ticket->created_at->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT: KIRI (CHAT) & KANAN (SIDEBAR) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            {{-- KOLOM KIRI - Chat & Detail Laporan (2/3) --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Detail Laporan --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-bold text-gray-800">Detail Laporan</h2>
                    </div>
                    <div class="px-5 py-4 space-y-5">
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Email Pelapor</div>
                            <div class="text-sm text-gray-700">{{ $ticket->client_email }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Deskripsi Masalah
                            </div>
                            <div
                                class="bg-gray-50 rounded-lg px-4 py-3 text-sm text-gray-600 leading-relaxed whitespace-pre-line border border-gray-100">
                                {{ $ticket->description }}
                            </div>
                        </div>

                        {{-- AI Summary --}}
                        @if ($ticket->ai_summary)
                            <div
                                class="mt-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-100 rounded-xl">
                                <div class="flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Rangkuman AI</span>
                                    @if (is_null($ticket->priority))
                                        <span
                                            class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Memproses...</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $ticket->ai_summary }}</p>
                                @if ($ticket->priority)
                                    <div class="mt-3 flex items-center gap-2 pt-2 border-t border-indigo-100">
                                        <span class="text-[10px] text-gray-500">Prioritas AI:</span>
                                        <span
                                            class="text-[10px] font-semibold px-2 py-0.5 rounded-full
                                            {{ $ticket->priority === 'high' ? 'bg-red-100 text-red-700' : '' }}
                                            {{ $ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                            {{ $ticket->priority === 'low' ? 'bg-green-100 text-green-700' : '' }}">
                                            {{ $ticket->priority === 'high' ? 'Tinggi' : ($ticket->priority === 'medium' ? 'Sedang' : 'Rendah') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @elseif(is_null($ticket->priority))
                            <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-xl animate-pulse">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-4 h-4 bg-gray-300 rounded-full"></div>
                                    <div class="h-3 bg-gray-300 rounded w-32"></div>
                                </div>
                                <div class="h-4 bg-gray-200 rounded w-full mb-2"></div>
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            </div>
                        @endif

                        {{-- Lampiran --}}
                        @if ($ticket->attachment)
                            <div>
                                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Lampiran
                                    ({{ count(explode(',', $ticket->attachment)) }} Gambar)</div>
                                <div class="flex flex-wrap gap-3">
                                    @foreach (explode(',', $ticket->attachment) as $file)
                                        <div class="group relative">
                                            <a href="{{ Storage::url($file) }}" target="_blank"
                                                class="flex flex-col items-center gap-2 p-2 bg-gray-50 border border-gray-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 transition-all w-32">
                                                <div class="w-full h-20 rounded-lg overflow-hidden bg-gray-200">
                                                    <img src="{{ Storage::url($file) }}" alt="attachment"
                                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                                </div>
                                                <div class="flex items-center gap-1.5 w-full">
                                                    <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span
                                                        class="text-[10px] font-medium text-gray-500 truncate">{{ basename($file) }}</span>
                                                </div>
                                            </a>
                                            <span
                                                class="absolute -top-2 -right-2 bg-white border border-gray-200 text-[10px] font-bold text-gray-400 w-5 h-5 rounded-full flex items-center justify-center shadow-sm">
                                                {{ $loop->iteration }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- PERCAKAPAN (CHAT) --}}
                @livewire('admin.ticket-chat', ['ticketId' => $ticket->id], key($ticket->id))

                {{-- Riwayat Status --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-bold text-gray-800">Riwayat Status</h2>
                    </div>
                    <div class="px-5 py-4">
                        <div class="flex flex-col">
                            @forelse($ticket->histories as $history)
                                @php
                                    $hsk = strtolower($history->status);
                                    $hlbl =
                                        ['open' => 'Open', 'in_progress' => 'In Progress', 'closed' => 'Closed'][$hsk] ??
                                        ucfirst($hsk);
                                @endphp
                                <div class="flex gap-3 relative {{ !$loop->last ? 'pb-5' : '' }}">
                                    @if (!$loop->last)
                                        <div class="absolute left-3.5 top-7 bottom-0 w-px bg-gray-200"></div>
                                    @endif
                                    <div
                                        class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-center z-10
                                        {{ $loop->last ? ($hsk === 'closed' ? 'bg-gray-100 text-gray-400' : 'bg-indigo-100 text-indigo-500') : 'bg-green-100 text-green-500' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if (!$loop->last)
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $icons[$hsk] ?? $icons['open'] }}" />
                                            @endif
                                        </svg>
                                    </div>
                                    <div class="flex-1 pt-0.5">
                                        <div class="text-sm font-semibold text-gray-800">
                                            {{ $loop->first ? 'Tiket Dibuat' : 'Status → ' . $hlbl }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-0.5 leading-relaxed">
                                            {{ $history->note ?? ($notes[$hsk] ?? '') }}
                                            @if ($history->changed_by && $history->changed_by !== 'System')
                                                <span class="text-indigo-500 font-semibold"> ·
                                                    {{ $history->changed_by }}</span>
                                            @endif
                                        </div>
                                        <div class="font-mono text-xs text-gray-300 mt-1">
                                            {{ $history->created_at->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-400">Belum ada riwayat.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN - SIDEBAR (Update Status, Info Pelapor, Hapus Tiket) --}}
            <div class="lg:col-span-1 space-y-4 lg:sticky lg:top-28">

                {{-- Update Status --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-bold text-gray-800">Update Status</h2>
                    </div>
                    <div class="px-5 py-4">
                        <form method="POST" action="{{ route('admin.tickets.update-status', $ticket->id) }}"
                            id="statusForm">
                            @csrf
                            @method('PATCH')
                            <div class="space-y-3">
                                <select name="status" id="statusSelect"
                                    {{ $ticket->status === 'closed' || (isset($statusCooldownUntil) && $statusCooldownUntil > time()) ? 'disabled' : '' }}
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 {{ $ticket->status === 'closed' || (isset($statusCooldownUntil) && $statusCooldownUntil > time()) ? 'bg-gray-100 cursor-not-allowed' : 'bg-gray-50' }} outline-none appearance-none focus:border-indigo-400 focus:bg-white transition-all">

                                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}
                                        {{ $ticket->status === 'in_progress' ? 'disabled' : '' }}>
                                        Open {{ $ticket->status === 'in_progress' ? '(Tidak tersedia)' : '' }}
                                    </option>

                                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In
                                        Progress</option>
                                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed
                                    </option>
                                </select>

                                <textarea name="note" rows="3"
                                    {{ $ticket->status === 'closed' || (isset($statusCooldownUntil) && $statusCooldownUntil > time()) ? 'disabled' : '' }}
                                    placeholder="Catatan perubahan status (opsional)…"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-700 {{ $ticket->status === 'closed' || (isset($statusCooldownUntil) && $statusCooldownUntil > time()) ? 'bg-gray-100 cursor-not-allowed' : 'bg-gray-50' }} outline-none resize-none placeholder-gray-400 focus:border-indigo-400 focus:bg-white transition-all"></textarea>

                                @if ($ticket->status !== 'closed')
                                    @if (isset($statusCooldownUntil) && $statusCooldownUntil > time())
                                        <button type="button" disabled
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-gray-500 bg-gray-100 cursor-not-allowed">
                                            Status Tidak Dapat Diubah
                                        </button>
                                        <div class="mt-2 px-3 py-2 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-700 text-center"
                                            id="statusCooldownMessage">
                                            Mohon tunggu <span id="statusCooldownTimer">0</span> detik sebelum mengubah status
                                            lagi.
                                        </div>
                                    @else
                                        <button type="submit" id="statusButton"
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-indigo-500 hover:bg-indigo-600 transition-all shadow-sm">
                                            Simpan Status
                                        </button>
                                    @endif
                                @else
                                    <div
                                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-gray-500 bg-gray-100 cursor-not-allowed">
                                        Status Tidak Dapat Diubah (Tiket Closed)
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Info Pelapor --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-bold text-gray-800">Info Pelapor</h2>
                    </div>
                    <div class="px-5 py-4">
                        <div class="divide-y divide-gray-50 space-y-0">
                            <div class="flex items-center justify-between py-2.5">
                                <span class="text-xs text-gray-400">Nama</span>
                                <span class="text-xs font-semibold text-gray-700">{{ $ticket->client_name }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2.5">
                                <span class="text-xs text-gray-400">Email</span>
                                <span
                                    class="text-xs font-semibold text-gray-700 break-all text-right max-w-36">{{ $ticket->client_email }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2.5">
                                <span class="text-xs text-gray-400">Dibuat</span>
                                <span
                                    class="text-xs font-mono font-semibold text-gray-700">{{ $ticket->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex items-center justify-between py-2.5">
                                <span class="text-xs text-gray-400">Diperbarui</span>
                                <span
                                    class="text-xs font-mono font-semibold text-gray-700">{{ $ticket->updated_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hapus Tiket (Zona Berbahaya) --}}
                <div class="bg-white border border-red-100 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-red-50">
                        <h2 class="text-sm font-bold text-red-500">Zona Berbahaya</h2>
                    </div>
                    <div class="px-5 py-4">
                        <p class="text-xs text-gray-400 leading-relaxed mb-4">
                            Menghapus tiket bersifat permanen. Semua data termasuk balasan dan riwayat akan ikut terhapus.
                        </p>
                        <form method="POST" action="{{ route('admin.tickets.destroy', $ticket->id) }}"
                            onsubmit="return confirm('Yakin ingin menghapus tiket {{ $ticket->ticket_code }}? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold text-red-600 bg-red-50 border border-red-100 hover:bg-red-500 hover:text-white hover:border-red-500 transition-all">
                                Hapus Tiket Ini
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            var statusExpiry = {{ isset($statusCooldownUntil) ? $statusCooldownUntil : 0 }};
            if (statusExpiry > 0) {
                updateStatusTimer();
                var statusInterval = setInterval(updateStatusTimer, 1000);
            }

            function updateStatusTimer() {
                var now = Math.floor(Date.now() / 1000);
                var remaining = statusExpiry - now;

                if (remaining <= 0) {
                    clearInterval(statusInterval);
                    var statusSelect = document.getElementById('statusSelect');
                    var statusTextarea = document.querySelector('textarea[name="note"]');
                    var statusButton = document.getElementById('statusButton');
                    var cooldownMessage = document.getElementById('statusCooldownMessage');

                    if (statusSelect) {
                        statusSelect.disabled = false;
                        statusSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
                        statusSelect.classList.add('bg-gray-50');
                    }
                    if (statusTextarea) {
                        statusTextarea.disabled = false;
                        statusTextarea.classList.remove('bg-gray-100', 'cursor-not-allowed');
                        statusTextarea.classList.add('bg-gray-50');
                    }
                    if (statusButton) {
                        statusButton.disabled = false;
                        statusButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                    if (cooldownMessage) cooldownMessage.remove();
                } else {
                    var timerElement = document.getElementById('statusCooldownTimer');
                    if (timerElement) {
                        timerElement.innerText = remaining;
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const statusForm = document.getElementById('statusForm');
                if (statusForm) {
                    statusForm.addEventListener('submit', function() {
                        if (window.NProgress) window.NProgress.start();
                        const btn = document.getElementById('statusButton');
                        if (btn) {
                            btn.disabled = true;
                            btn.innerHTML = 'Menyimpan...';
                        }
                    });
                }
            });
        </script>
    @endpush
