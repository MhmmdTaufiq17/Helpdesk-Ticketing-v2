@extends('layouts.user.app')

@section('title', 'Status Tiket #' . $ticket->ticket_code)

@section('content')
    <style>
        /* Animasi kustom yang tidak tersedia secara default di Tailwind */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes blinkDot {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.4;
                transform: scale(1.4);
            }
        }

        .animate-fade-up {
            animation: fadeUp 0.38s ease both;
        }

        .animate-blink {
            animation: blinkDot 1.4s infinite;
        }

        .delay-40 {
            animation-delay: 0.04s;
        }

        .delay-100 {
            animation-delay: 0.10s;
        }

        .delay-180 {
            animation-delay: 0.18s;
        }
    </style>

    @php
        $sk = strtolower($ticket->status);
        $slbl = ['open' => 'Open', 'in_progress' => 'In Progress', 'closed' => 'Closed'][$sk] ?? ucfirst($sk);
        $sico =
            [
                'open' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'in_progress' => 'M13 10V3L4 14h7v7l9-11h-7z',
                'closed' => 'M6 18L18 6M6 6l12 12',
            ][$sk] ?? 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z';

        // Tailwind Class Mappings
        $ringMap = [
            'open' => 'bg-blue-50 text-blue-600 ring-[6px] ring-blue-600/10',
            'in_progress' => 'bg-amber-50 text-amber-500 ring-[6px] ring-amber-500/10',
            'closed' => 'bg-slate-100 text-slate-400',
        ];
        $badgeMap = [
            'open' => 'bg-blue-100 text-blue-700',
            'in_progress' => 'bg-amber-50 text-amber-800',
            'closed' => 'bg-slate-100 text-slate-600',
        ];
        $dotMap = [
            'open' => 'bg-blue-700 animate-blink',
            'in_progress' => 'bg-amber-500 animate-blink',
            'closed' => 'bg-slate-400',
        ];
        $priMap = [
            'low' => ['cls' => 'bg-green-50 text-green-800', 'lbl' => 'Low'],
            'medium' => ['cls' => 'bg-amber-50 text-amber-800', 'lbl' => 'Medium'],
            'high' => ['cls' => 'bg-red-50 text-red-500', 'lbl' => 'High'],
        ];

        $pri = $priMap[$ticket->priority] ?? $priMap['medium'];
    @endphp

    <div class="max-w-[1100px] mx-auto px-6 pt-9 pb-20">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-1.5 text-[12.5px] text-slate-400 mb-7 animate-fade-up">
            <a href="{{ route('user.home') }}" class="text-indigo-600 font-medium hover:underline">Beranda</a>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span>Lacak Tiket</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-slate-900 font-semibold font-['DM_Mono',monospace]">{{ $ticket->ticket_code }}</span>
        </div>

        {{-- Hero card --}}
        <div
            class="bg-white rounded-[24px] border border-slate-200 mb-8 shadow-[0_2px_1px_rgba(0,0,0,.02),0_12px_40px_rgba(0,0,0,.04)] overflow-hidden animate-fade-up delay-40">
            <div class="p-8 flex items-center justify-between gap-8 flex-wrap border-b border-slate-100">
                <div class="flex items-center gap-6"> {{-- Icon wrapper dengan bayangan halus --}}
                    <div
                        class="w-16 h-16 shrink-0 rounded-[20px] flex items-center justify-center shadow-sm {{ $ringMap[$sk] ?? $ringMap['open'] }}">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                d="{{ $sico }}" />
                        </svg>
                    </div>

                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <p
                                class="font-['DM_Mono',monospace] text-2xl font-bold text-slate-900 tracking-tight m-0 uppercase">
                                {{ $ticket->ticket_code }}</p>
                            {{-- Dot indikator kecil di samping kode --}}
                            <span class="w-2 h-2 rounded-full {{ $dotMap[$sk] ?? $dotMap['open'] }}"></span>
                        </div>
                        <p class="text-[15px] text-slate-500 m-0 max-w-[480px] leading-relaxed">{{ $ticket->title }}</p>
                    </div>
                </div>

                <div class="flex flex-col items-end gap-3">
                    <span
                        class="inline-flex items-center gap-2.5 px-5 py-2 rounded-full text-[13px] font-bold tracking-wide {{ $badgeMap[$sk] ?? $badgeMap['open'] }} border border-black/5">
                        {{ $slbl }}
                    </span>
                    <div class="flex items-center gap-1.5 text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-[12px]">Update: {{ $ticket->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            {{-- Info Bar di bawah --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 bg-slate-50/50">
                <div class="px-8 py-5 border-r border-slate-100">
                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-[1.5px] mb-1">Pelapor</div>
                    <div class="text-[14px] font-semibold text-slate-700 truncate">{{ $ticket->client_name }}</div>
                </div>
                <div class="px-8 py-5 border-r border-slate-100">
                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-[1.5px] mb-1">Prioritas</div>
                    <div class="text-[14px] font-semibold text-slate-700 flex items-center gap-2">
                        <span
                            class="w-2 h-2 rounded-full {{ $ticket->priority === 'high' ? 'bg-red-500' : ($ticket->priority === 'medium' ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                        {{ $pri['lbl'] }}
                    </div>
                </div>
                <div class="px-8 py-5 border-r border-slate-100">
                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-[1.5px] mb-1">Kategori</div>
                    <div class="text-[14px] font-semibold text-slate-700">
                        {{ $ticket->category ? $ticket->category->category_name : 'General' }}</div>
                </div>
                <div class="px-8 py-5">
                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-[1.5px] mb-1">Dibuat Pada</div>
                    <div class="text-[14px] font-semibold text-slate-700 font-['DM_Mono']">
                        {{ $ticket->created_at->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        {{-- Main layout --}}
        <div class="grid grid-cols-1 md:grid-cols-[1fr_300px] gap-6 items-start animate-fade-up delay-100">

            {{-- Left: detail + replies + timeline --}}
            <div class="flex flex-col gap-5">

                {{-- Detail Tiket Card --}}
                <div
                    class="bg-white rounded-[16px] border border-slate-200 shadow-[0_2px_1px_rgba(0,0,0,.02),0_8px_32px_rgba(0,0,0,.06)] overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2.5">
                        <div
                            class="w-[30px] h-[30px] bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <span class="text-[13.5px] font-bold text-slate-900">Detail Tiket</span>
                    </div>
                    <div class="p-5">
                        <div class="mb-5">
                            <div class="text-[11px] font-semibold tracking-wide uppercase text-slate-400 mb-1.5">Judul
                                Laporan</div>
                            <div class="text-[14px] font-medium text-slate-900 leading-relaxed">{{ $ticket->title }}</div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                            <div>
                                <div class="text-[11px] font-semibold tracking-wide uppercase text-slate-400 mb-1.5">
                                    Kategori</div>
                                @if ($ticket->category)
                                    <span
                                        class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[12.5px] font-semibold">{{ $ticket->category->category_name }}</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[12.5px] font-semibold">Tidak
                                        ada</span>
                                @endif
                            </div>
                            <div>
                                <div class="text-[11px] font-semibold tracking-wide uppercase text-slate-400 mb-1.5">
                                    Prioritas</div>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-[12.5px] font-semibold {{ $pri['cls'] }}">{{ $pri['lbl'] }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="text-[11px] font-semibold tracking-wide uppercase text-slate-400 mb-1.5">Deskripsi
                                Masalah</div>
                            <div
                                class="bg-slate-50 rounded-[10px] p-4 font-normal text-[13.5px] text-slate-700 whitespace-pre-line leading-relaxed">
                                {{ $ticket->description }}</div>
                        </div>
                    </div>
                </div>

                {{-- Chat Real-time dengan Admin --}}
                @livewire('user.ticket-chat', ['ticketId' => $ticket->id], key($ticket->id))

                {{-- Riwayat Status Card --}}
                <div
                    class="bg-white rounded-[16px] border border-slate-200 shadow-[0_2px_1px_rgba(0,0,0,.02),0_8px_32px_rgba(0,0,0,.06)] overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2.5">
                        <div
                            class="w-[30px] h-[30px] bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-[13.5px] font-bold text-slate-900">Riwayat Status</span>
                    </div>
                    <div class="p-5">
                        <div class="flex flex-col">
                            @php
                                $icons = [
                                    'open' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                    'in_progress' => 'M13 10V3L4 14h7v7l9-11h-7z',
                                    'closed' => 'M5 13l4 4L19 7',
                                ];
                                $notes = [
                                    'open' => 'Tiket masuk dan menunggu penanganan tim support',
                                    'in_progress' => 'Tim sedang menganalisis dan mengerjakan solusi',
                                    'closed' => 'Tiket telah diselesaikan dan ditutup',
                                ];
                            @endphp

                            @foreach ($ticket->histories as $i => $history)
                                @php
                                    $hsk = strtolower($history->status);
                                    $hlbl =
                                        ['open' => 'Open', 'in_progress' => 'In Progress', 'closed' => 'Closed'][
                                            $hsk
                                        ] ?? ucfirst($hsk);
                                    $isLast = $loop->last;

                                    // Map classes for dots
                                    $dotCls = 'bg-green-50 text-green-600'; // ok
                                    if ($isLast) {
                                        $dotCls =
                                            $hsk === 'closed'
                                                ? 'bg-slate-100 text-slate-400'
                                                : 'bg-indigo-50 text-indigo-600';
                                    }
                                @endphp
                                <div class="flex gap-3.5 relative">
                                    @if (!$isLast)
                                        <div class="absolute left-[15px] top-[33px] bottom-0 w-[1.5px] bg-slate-200"></div>
                                    @endif
                                    <div
                                        class="w-8 h-8 shrink-0 rounded-full relative z-10 flex items-center justify-center {{ $dotCls }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            @if (!$isLast)
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $icons[$hsk] ?? $icons['open'] }}" />
                                            @endif
                                        </svg>
                                    </div>
                                    <div class="flex-1 pb-5.5 pt-1">
                                        <div class="text-[13px] font-semibold text-slate-900">
                                            @if ($loop->first)
                                                Tiket Dibuat
                                            @else
                                                Status diubah → {{ $hlbl }}
                                            @endif
                                        </div>
                                        <div class="text-[12px] text-slate-500 mt-0.5 leading-relaxed">
                                            {{ $history->note ?? ($notes[$hsk] ?? '') }}
                                            @if ($history->changed_by && $history->changed_by !== 'System')
                                                <span class="text-indigo-600 font-semibold"> ·
                                                    {{ $history->changed_by }}</span>
                                            @endif
                                        </div>
                                        <div class="font-['DM_Mono',monospace] text-[11px] text-slate-400 mt-1.5">
                                            {{ $history->created_at->format('d M Y, H:i') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right: sidebar --}}
            <div class="flex flex-col gap-4">

                <div
                    class="bg-white rounded-[16px] border border-slate-200 shadow-[0_2px_1px_rgba(0,0,0,.02),0_8px_32px_rgba(0,0,0,.06)] overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2.5">
                        <div
                            class="w-[30px] h-[30px] bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="text-[13.5px] font-bold text-slate-900">Pelapor</span>
                    </div>
                    <div class="p-5">
                        <div class="flex flex-col divide-y divide-slate-200">
                            <div class="flex justify-between items-center py-2.5 first:pt-0 last:pb-0 gap-3">
                                <span class="text-[12px] text-slate-500 shrink-0">Nama</span>
                                <span
                                    class="text-[12.5px] font-semibold text-slate-900 text-right break-all">{{ $ticket->client_name }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2.5 first:pt-0 last:pb-0 gap-3">
                                <span class="text-[12px] text-slate-500 shrink-0">Email</span>
                                <span
                                    class="text-[11.5px] font-semibold text-slate-900 text-right break-all">{{ $ticket->client_email }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2.5 first:pt-0 last:pb-0 gap-3">
                                <span class="text-[12px] text-slate-500 shrink-0">Tanggal</span>
                                <span
                                    class="font-['DM_Mono',monospace] text-[11.5px] font-semibold text-slate-900 text-right break-all">{{ $ticket->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-[16px] border border-slate-200 shadow-[0_2px_1px_rgba(0,0,0,.02),0_8px_32px_rgba(0,0,0,.06)] overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center gap-2.5">
                        <div
                            class="w-[30px] h-[30px] bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <span class="text-[13.5px] font-bold text-slate-900">Aksi</span>
                    </div>
                    <div class="p-5">
                        <div class="flex flex-col gap-2.5">
                            <a href="{{ route('user.tickets.create') }}"
                                class="flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white rounded-[10px] text-[13px] font-semibold font-['Sora',sans-serif] transition-all hover:bg-indigo-700 hover:-translate-y-px shadow-[0_4px_14px_rgba(91,94,244,.3)] hover:shadow-[0_6px_20px_rgba(91,94,244,.4)]">
                                <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Buat Tiket Baru
                            </a>
                            <a href="{{ route('user.home') }}"
                                class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-slate-700 border-[1.5px] border-slate-200 rounded-[10px] text-[13px] font-semibold font-['Sora',sans-serif] transition-all hover:bg-slate-50 hover:border-slate-400 hover:text-slate-900">
                                <svg class="w-[15px] h-[15px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>

                <div class="rounded-[16px] bg-indigo-50 border border-indigo-600/15 p-5">
                    <div class="text-[13px] font-bold text-indigo-600 mb-1.5">💬 Butuh Bantuan?</div>
                    <div class="text-[12px] text-slate-700 leading-relaxed mb-3.5">Tim kami siap membantu. Hubungi kami
                        jika ada pertanyaan lebih lanjut seputar tiket Anda.</div>
                    <div class="flex flex-col gap-2">
                        <a href="mailto:support@helpdesk.com"
                            class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-indigo-600 bg-white border border-indigo-600/15 rounded-lg px-3 py-2 transition-colors hover:bg-indigo-100">
                            <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            support@helpdesk.com
                        </a>
                        <a href="tel:+622112345678"
                            class="inline-flex items-center gap-1.5 text-[12px] font-semibold text-indigo-600 bg-white border border-indigo-600/15 rounded-lg px-3 py-2 transition-colors hover:bg-indigo-100">
                            <svg class="w-[13px] h-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            (021) 1234-5678
                        </a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Info strip --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8 animate-fade-up delay-180">

            <div
                class="group bg-white/70 backdrop-blur-md border border-white rounded-2xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="flex flex-col gap-4">
                    <div
                        class="w-12 h-12 shrink-0 rounded-2xl flex items-center justify-center bg-indigo-50 text-indigo-600 transition-colors group-hover:bg-indigo-600 group-hover:text-white shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-[15px] font-bold text-slate-900 mb-1">Respon Cepat</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Tim kami merespons dalam waktu kurang dari
                            24 jam kerja.</p>
                    </div>
                </div>
            </div>

            <div
                class="group bg-white/70 backdrop-blur-md border border-white rounded-2xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="flex flex-col gap-4">
                    <div
                        class="w-12 h-12 shrink-0 rounded-2xl flex items-center justify-center bg-violet-50 text-violet-600 transition-colors group-hover:bg-violet-600 group-hover:text-white shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-[15px] font-bold text-slate-900 mb-1">Notifikasi Email</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Setiap update status akan langsung dikirim
                            ke kotak masuk Anda.</p>
                    </div>
                </div>
            </div>

            <div
                class="group bg-white/70 backdrop-blur-md border border-white rounded-2xl p-5 shadow-[0_8px_30px_rgb(0,0,0,0.04)] transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                <div class="flex flex-col gap-4">
                    <div
                        class="w-12 h-12 shrink-0 rounded-2xl flex items-center justify-center bg-emerald-50 text-emerald-600 transition-colors group-hover:bg-emerald-600 group-hover:text-white shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-[15px] font-bold text-slate-900 mb-1">Data Aman</h3>
                        <p class="text-[12.5px] text-slate-500 leading-relaxed">Privasi dan informasi Anda dilindungi
                            dengan standar keamanan tinggi.</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                if (window.Toast) Toast.fire({
                    icon: 'success',
                    title: '{{ addslashes(session('success')) }}'
                });
            @endif
        });
    </script>
@endsection
