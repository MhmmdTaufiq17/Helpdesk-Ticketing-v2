<div>
    {{-- Tombol Export --}}
    <button wire:click="openModal"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-[#5b5ef4] hover:bg-indigo-600 transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        Ekspor Laporan
    </button>

    {{-- Modal --}}
    <div x-data="{ open: @entangle('showModal') }" x-show="open" x-cloak class="fixed inset-0 z-50" style="display: none;">

        {{-- Backdrop dengan blur, tapi sidebar tidak kena karena posisi --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" x-on:click="open = false"
            style="z-index: 1;"></div>

        {{-- Modal panel --}}
        <div class="relative flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0"
            style="z-index: 2;">
            <div
                class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="px-6 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-indigo-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                Ekspor Laporan Statistik
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Pilih format file yang ingin Anda ekspor.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        {{-- Excel --}}
                        <label
                            class="flex items-center p-3 rounded-xl cursor-pointer hover:bg-gray-50 transition-all border border-gray-200">
                            <input type="radio" name="exportType" value="excel" wire:model="exportType"
                                class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 focus:outline-none">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Microsoft Excel (.xlsx)</span>
                                <span class="block text-xs text-gray-500">Ekspor ke format Excel yang dapat
                                    diedit</span>
                            </div>
                            <svg class="w-8 h-8 ml-auto text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </label>

                        {{-- CSV --}}
                        <label
                            class="flex items-center p-3 rounded-xl cursor-pointer hover:bg-gray-50 transition-all border border-gray-200">
                            <input type="radio" name="exportType" value="csv" wire:model="exportType"
                                class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 focus:outline-none">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">CSV (.csv)</span>
                                <span class="block text-xs text-gray-500">Ekspor ke format CSV (Comma Separated
                                    Values)</span>
                            </div>
                            <svg class="w-8 h-8 ml-auto text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </label>

                        {{-- PDF --}}
                        <label
                            class="flex items-center p-3 rounded-xl cursor-pointer hover:bg-gray-50 transition-all border border-gray-200">
                            <input type="radio" name="exportType" value="pdf" wire:model="exportType"
                                class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 focus:outline-none">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">PDF (.pdf)</span>
                                <span class="block text-xs text-gray-500">Ekspor ke format PDF untuk dicetak</span>
                            </div>
                            <svg class="w-8 h-8 ml-auto text-red-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </label>
                    </div>
                </div>

                <div class="px-6 py-3 bg-gray-50 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="export" wire:loading.attr="disabled"
                        class="inline-flex justify-center w-full px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-xl shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                        <span wire:loading.remove wire:target="export">Ekspor Sekarang</span>
                        <span wire:loading wire:target="export">
                            <svg class="inline w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                    <button type="button" x-on:click="open = false"
                        class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-medium text-gray-700 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        input[type="radio"]:focus {
            outline: none;
        }

        input[type="radio"]:checked {
            background-color: #5b5ef4;
            border-color: #5b5ef4;
        }
    </style>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('download-file', (url) => {
                    window.open(url, '_blank');
                });

                Livewire.on('success', (message) => {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(message);
                    }
                });

                Livewire.on('error', (message) => {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(message);
                    }
                });
            });
        </script>
    @endpush
</div>
