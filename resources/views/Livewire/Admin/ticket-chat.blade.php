<div class="flex flex-col bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden shadow-sm h-[600px]"
    x-data="{
        scrollToBottom() {
            if ($refs.chatContainer) {
                $refs.chatContainer.scrollTop = $refs.chatContainer.scrollHeight;
            }
        }
    }" x-init="scrollToBottom();
    $wire.on('scroll-to-bottom', () => {
        setTimeout(() => { scrollToBottom() }, 50);
    });">
    {{-- Header --}}
    <div
        class="px-5 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between shrink-0 z-10 shadow-sm">
        <div class="flex items-center gap-3">
            <div
                class="relative flex items-center justify-center w-10 h-10 rounded-xl bg-[#5b5ef4] text-white shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                @if ($ticket->status !== 'closed')
                    <span
                        class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
                @endif
            </div>
            <div>
                <h2 class="text-[14px] font-bold text-slate-800 leading-tight">
                    {{ $ticket->client_name }}
                </h2>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">
                {{ count($messages) }} Messages
            </span>
        </div>
    </div>

    {{-- Chat Body --}}
    <div x-ref="chatContainer" class="flex-1 overflow-y-auto p-5 space-y-4 scroll-smooth">
        @forelse ($messages as $msg)
            <div
                class="flex flex-col {{ $msg['sender_type'] === 'admin' ? 'items-end' : 'items-start' }} animate-fade-in">
                <div
                    class="flex items-end gap-2 max-w-[85%] {{ $msg['sender_type'] === 'admin' ? 'flex-row-reverse' : 'flex-row' }}">

                    {{-- Avatar untuk Client/User --}}
                    @if ($msg['sender_type'] !== 'admin')
                        <div
                            class="w-8 h-8 rounded-xl flex-shrink-0 flex items-center justify-center text-xs font-bold bg-slate-300 text-slate-600 shadow-sm">
                            {{ strtoupper(substr($msg['sender_name'] ?? $ticket->client_name, 0, 2)) }}
                        </div>
                    @endif

                    {{-- Avatar untuk Admin (yang login) --}}
                    @if ($msg['sender_type'] === 'admin')
                        <div
                            class="w-8 h-8 rounded-xl flex-shrink-0 flex items-center justify-center text-xs font-bold bg-[#5b5ef4] text-white shadow-sm">
                            {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                        </div>
                    @endif

                    {{-- Bubble --}}
                    <div
                        class="relative group px-4 py-2.5 shadow-sm
                        {{ $msg['sender_type'] === 'admin'
                            ? 'bg-[#5b5ef4] text-white rounded-2xl rounded-tr-sm'
                            : 'bg-white border border-slate-200 text-slate-700 rounded-2xl rounded-tl-sm' }}">

                        {{-- Sender Name --}}
                        @if ($msg['sender_type'] !== 'admin')
                            <p class="text-[10px] font-bold text-[#5b5ef4] mb-0.5">{{ $msg['sender_name'] }}</p>
                        @else
                            <p class="text-[10px] font-bold text-indigo-200 mb-0.5">Admin Support</p>
                        @endif

                        <p class="text-[13px] leading-relaxed whitespace-pre-wrap break-words">{{ $msg['message'] }}</p>

                        <div class="flex items-center justify-end gap-1 mt-1">
                            <span
                                class="text-[9px] {{ $msg['sender_type'] === 'admin' ? 'text-indigo-200' : 'text-slate-400' }}">
                                {{ \Carbon\Carbon::parse($msg['timestamp'])->format('H:i') }}
                            </span>
                            @if ($msg['sender_type'] === 'admin')
                                <svg class="w-3 h-3 text-indigo-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="h-full flex flex-col items-center justify-center text-center opacity-50 select-none">
                <div class="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <p class="text-[13px] font-medium text-slate-500">Belum ada obrolan.</p>
                <p class="text-[11px] text-slate-400">Pesan yang dikirim akan muncul di sini.</p>
            </div>
        @endforelse
    </div>

    {{-- Footer Input --}}
    @if ($ticket->status !== 'closed')
        <div class="p-3 bg-white border-t border-slate-200 shrink-0">
            <form wire:submit.prevent="sendMessage" class="flex items-end gap-2 relative">

                <div
                    class="flex-1 bg-slate-50 border border-slate-200 rounded-2xl px-4 py-2 focus-within:border-[#5b5ef4] focus-within:ring-2 focus-within:ring-indigo-50 transition-all shadow-inner">
                    <textarea wire:model="message" wire:keydown.enter="sendMessage" maxlength="2000"
                        placeholder="Ketik balasan untuk {{ $ticket->client_name }}..."
                        class="w-full bg-transparent text-[13px] text-slate-800 outline-none resize-none max-h-32 min-h-[40px] py-1.5"
                        rows="1" x-data="{ resize() { $el.style.height = '40px';
                                $el.style.height = $el.scrollHeight + 'px' } }" x-init="resize()" @input="resize()">
                    </textarea>
                </div>

                {{-- Tombol AI --}}
                <button type="button" wire:click="generateAIReply" wire:loading.attr="disabled"
                    wire:target="generateAIReply"
                    class="h-11 w-11 flex items-center justify-center rounded-xl font-bold text-white bg-purple-500 hover:bg-purple-600 transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    title="Generate balasan dengan AI">

                    <span wire:loading.remove wire:target="generateAIReply">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </span>

                    <span wire:loading wire:target="generateAIReply">
                        <svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                </button>

                {{-- Tombol Kirim --}}
                <button type="submit" wire:loading.attr="disabled" wire:target="sendMessage"
                    class="h-11 min-w-[44px] flex items-center justify-center rounded-xl font-bold text-white bg-[#5b5ef4] hover:bg-indigo-700 active:bg-indigo-800 transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">

                    <span wire:loading.remove wire:target="sendMessage">
                        <svg class="w-5 h-5 translate-x-[-1px] translate-y-[1px]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </span>

                    <span wire:loading wire:target="sendMessage">
                        <svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                </button>
            </form>
            @error('message')
                <p class="text-[11px] text-red-500 font-medium mt-1.5 ml-2">{{ $message }}</p>
            @enderror
        </div>
    @else
        <div class="p-4 bg-slate-100 border-t border-slate-200 shrink-0 text-center">
            <p class="text-[12px] font-medium text-slate-500 flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Tiket ini telah ditutup, Anda tidak dapat membalas lagi.
            </p>
        </div>
    @endif
</div>

@push('styles')
    <style>
        @keyframes fadeInSlide {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInSlide 0.25s ease-out forwards;
        }

        textarea::-webkit-scrollbar {
            width: 4px;
        }

        textarea::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.addEventListener('beforeunload', () => {
            if (keepAliveInterval) clearInterval(keepAliveInterval);
        });
    </script>
@endpush

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('email-sent', (message) => {
            if (typeof toastr !== 'undefined') {
                toastr.success(message);
            } else {
                alert(message);
            }
        });

        Livewire.on('status-updated', (data) => {
            const statusBadge = document.getElementById('ticket-status-badge');
            if (statusBadge) {
                statusBadge.textContent = data.status.replace('_', ' ');
                statusBadge.className = `badge badge-${getStatusColor(data.status)}`;
            }
        });
    });

    function getStatusColor(status) {
        const colors = {
            'open': 'warning',
            'in_progress': 'info',
            'closed': 'success'
        };
        return colors[status] || 'secondary';
    }
</script>
