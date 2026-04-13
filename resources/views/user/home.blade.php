@extends('layouts.user.app')

@section('title', 'Buat Tiket Baru')

@section('content')
    {{--
    [FIX #4 — Stored XSS]
    Seluruh output data dari user/DB menggunakan {{ }} yang auto-escape HTML.
    JANGAN pernah gunakan {!! !!} untuk data yang berasal dari input user.
    Perubahan dari versi sebelumnya ditandai komentar "FIXED".
--}}

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        /* Animasi kustom yang tidak ada bawaannya di Tailwind */
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-slide-up {
            animation: fadeSlideUp 0.45s ease both;
        }

        .delay-100 {
            animation-delay: 0.10s;
        }

        .delay-180 {
            animation-delay: 0.18s;
        }
    </style>

    <div
        class="min-h-[calc(100vh-64px)] grid grid-cols-1 min-[860px]:grid-cols-[1fr_420px] max-w-[1080px] mx-auto pt-12 px-6 pb-16 gap-12 items-start">

        <div
            class="bg-white rounded-[20px] shadow-[0_2px_1px_rgba(0,0,0,.03),0_8px_32px_rgba(0,0,0,.06)] overflow-hidden animate-fade-slide-up">

            <div class="px-9 pt-8 pb-7 border-b border-slate-200 flex items-center gap-4">
                <div
                    class="w-[46px] h-[46px] bg-indigo-50 rounded-[13px] flex items-center justify-center shrink-0 text-indigo-600">
                    <svg class="w-[22px] h-[22px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
                <div>
                    <p class="text-[20px] font-bold text-slate-950 tracking-tight mb-1">Buat Tiket Baru</p>
                    <p class="text-[13px] text-slate-500 m-0">Isi formulir di bawah untuk melaporkan masalah Anda</p>
                </div>
            </div>

            <div class="px-9 py-8">

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-500/20 rounded-[9px] py-3.5 px-4 mb-7 flex gap-3 items-start">
                        <svg class="w-[18px] h-[18px] text-red-500 shrink-0 mt-px" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="text-[13px] text-red-500">
                            <strong class="block mb-1.5 font-semibold">Terdapat kesalahan:</strong>
                            <ul class="m-0 pl-4 list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>{{-- FIXED: {{ }} bukan {!! !!} --}}
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form action="{{ route('user.tickets.store') }}" method="POST" enctype="multipart/form-data"
                    id="ticketForm">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5.5">
                        <div class="mb-5.5">
                            <label for="client_name"
                                class="flex items-center gap-[7px] text-[13px] font-semibold text-slate-800 mb-2">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Nama Anda <span class="text-red-500">*</span>
                            </label>
                            {{-- FIXED: old() di-escape otomatis oleh {{ }} --}}
                            <input type="text" name="client_name" id="client_name" value="{{ old('client_name') }}"
                                class="w-full bg-slate-50 border-[1.5px] rounded-[9px] px-3.5 py-2.5 text-[14px] text-slate-950 transition-all outline-none placeholder:text-slate-400 focus:bg-white focus:ring-[3px] focus:ring-indigo-600/10 {{ $errors->has('client_name') ? 'border-red-500 bg-red-50' : 'border-transparent focus:border-indigo-600' }}"
                                placeholder="Nama lengkap" maxlength="255" required>
                            @error('client_name')
                                <p class="mt-1.5 text-[12px] text-red-500 flex items-center gap-1.5">
                                    <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="mb-5.5">
                            <label for="client_email"
                                class="flex items-center gap-[7px] text-[13px] font-semibold text-slate-800 mb-2">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="client_email" id="client_email" value="{{ old('client_email') }}"
                                class="w-full bg-slate-50 border-[1.5px] rounded-[9px] px-3.5 py-2.5 text-[14px] text-slate-950 transition-all outline-none placeholder:text-slate-400 focus:bg-white focus:ring-[3px] focus:ring-indigo-600/10 {{ $errors->has('client_email') ? 'border-red-500 bg-red-50' : 'border-transparent focus:border-indigo-600' }}"
                                placeholder="contoh@email.com" maxlength="255" required>
                            <p class="mt-1.5 text-[12px] text-slate-500 flex items-center gap-1.5">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Notifikasi dikirim ke sini
                            </p>
                            @error('client_email')
                                <p class="mt-1.5 text-[12px] text-red-500 flex items-center gap-1.5">
                                    <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-5.5">
                        <label for="title"
                            class="flex items-center gap-[7px] text-[13px] font-semibold text-slate-800 mb-2">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            Judul Laporan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}"
                            class="w-full bg-slate-50 border-[1.5px] rounded-[9px] px-3.5 py-2.5 text-[14px] text-slate-950 transition-all outline-none placeholder:text-slate-400 focus:bg-white focus:ring-[3px] focus:ring-indigo-600/10 {{ $errors->has('title') ? 'border-red-500 bg-red-50' : 'border-transparent focus:border-indigo-600' }}"
                            placeholder="Ringkasan singkat masalah Anda" maxlength="255" required>
                        @error('title')
                            <p class="mt-1.5 text-[12px] text-red-500 flex items-center gap-1.5">
                                <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mb-5.5 mt-5">
                        <label for="category_id"
                            class="flex items-center gap-[7px] text-[13px] font-semibold text-slate-800 mb-2">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Kategori <span class="font-normal text-slate-500 text-[11.5px] ml-0.5">(opsional)</span>
                        </label>
                        <select name="category_id" id="category_id"
                            class="appearance-none bg-[url('data:image/svg+xml,%3Csvg_xmlns=\'http://www.w3.org/2000/svg\'_width=\'12\'_height=\'12\'_viewBox=\'0_0_24_24\'_fill=\'none\'_stroke=\'%238a8a9a\'_stroke-width=\'2\'_stroke-linecap=\'round\'_stroke-linejoin=\'round\'%3E%3Cpolyline_points=\'6_9_12_15_18_9\'%3E%3C/polyline%3E%3C/svg%3E')] bg-no-repeat bg-[position:right_14px_center] pr-10 cursor-pointer w-full bg-slate-50 border-[1.5px] rounded-[9px] px-3.5 py-2.5 text-[14px] text-slate-950 transition-all outline-none focus:bg-white focus:ring-[3px] focus:ring-indigo-600/10 {{ $errors->has('category_id') ? 'border-red-500 bg-red-50' : 'border-transparent focus:border-indigo-600' }}">
                            <option value="">Pilih kategori masalah…</option>
                            @foreach ($categories as $category)
                                {{-- FIXED: {{ }} pada category_name --}}
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1.5 text-[12px] text-red-500 flex items-center gap-1.5">
                                <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mb-5.5 mt-5">
                        <label for="description"
                            class="flex items-center gap-[7px] text-[13px] font-semibold text-slate-800 mb-2">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            Deskripsi Masalah <span class="text-red-500">*</span>
                        </label>
                        {{-- FIXED: old('description') di-escape oleh {{ }} dalam textarea --}}
                        <textarea name="description" id="description"
                            class="resize-y min-h-[130px] leading-relaxed w-full bg-slate-50 border-[1.5px] rounded-[9px] px-3.5 py-2.5 text-[14px] text-slate-950 transition-all outline-none placeholder:text-slate-400 focus:bg-white focus:ring-[3px] focus:ring-indigo-600/10 {{ $errors->has('description') ? 'border-red-500 bg-red-50' : 'border-transparent focus:border-indigo-600' }}"
                            placeholder="Jelaskan masalah secara detail…" maxlength="5000" required>{{ old('description') }}</textarea>

                        {{-- Counter & Hint --}}
                        <div class="flex justify-between items-center mt-1.5">
                            <p class="text-[12px] text-slate-500 flex items-center gap-1.5">
                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Semakin detail, semakin cepat diselesaikan
                            </p>
                            <p class="text-[12px] text-slate-500" id="descCounter">
                                <span id="descCount">0</span> / 5.000
                            </p>
                        </div>
                        @error('description')
                            <p class="mt-1.5 text-[12px] text-red-500 flex items-center gap-1.5">
                                <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mb-5.5 mt-5">
                        <label class="flex items-center gap-[7px] text-[13px] font-semibold text-slate-800 mb-2">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            Lampiran <span class="font-normal text-slate-500 text-[11.5px] ml-0.5">(opsional)</span>
                        </label>
                        <div class="relative bg-slate-50 border-[1.5px] border-dashed border-slate-300 rounded-[9px] p-5 text-center transition-all cursor-pointer hover:border-indigo-600 hover:bg-indigo-50 focus-within:border-indigo-600 focus-within:bg-indigo-50"
                            id="fileDropZone">
                            <input type="file" name="attachment[]" id="attachment"
                                accept="image/png, image/jpeg, image/jpg"
                                class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" multiple>

                            <div class="text-[28px] mb-2">📸</div>
                            <div class="text-[13px] font-medium text-slate-800">Klik atau seret gambar ke sini</div>
                            <div class="text-[12px] text-slate-500 mt-1">Hanya PNG, JPG, JPEG — Maks. 5 file (Masing-masing
                                5MB)</div>
                        </div>
                        <div id="fileNotification" style="display:none;"></div>
                        @error('attachment')
                            <p class="mt-1.5 text-[12px] text-red-500 flex items-center gap-1.5">
                                <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="mb-5.5 mt-5">
                        <label class="flex items-center gap-[7px] text-[13px] font-semibold text-slate-800 mb-2">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Verifikasi Keamanan <span class="text-red-500">*</span>
                        </label>
                        <div class="bg-slate-50 border border-slate-100 rounded-[9px] p-5 flex justify-center">
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        </div>
                        @error('g-recaptcha-response')
                            <p class="mt-1.5 text-[12px] text-red-500 flex items-center gap-1.5">
                                <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="h-px bg-slate-200 my-7"></div>

                    <div class="flex gap-3 flex-wrap">
                        <button type="submit" id="submitBtn"
                            class="flex-1 min-w-[160px] inline-flex items-center justify-center gap-2 bg-indigo-600 text-white border-none rounded-[9px] py-[13px] px-6 text-[14px] font-semibold cursor-pointer transition-all shadow-[0_4px_14px_rgba(79,70,229,.3)] hover:bg-indigo-700 hover:-translate-y-[1px] hover:shadow-[0_6px_20px_rgba(79,70,229,.4)] active:translate-y-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Kirim Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <aside class="flex flex-col gap-4 max-[860px]:order-first min-[860px]:order-none">
            <div
                class="bg-white rounded-[16px] p-[22px] shadow-[0_2px_1px_rgba(0,0,0,.03),0_4px_16px_rgba(0,0,0,.05)] animate-fade-slide-up delay-100">
                <p class="text-[11px] font-bold text-slate-500 tracking-[0.6px] uppercase m-0 mb-3.5">Cara Kerja</p>
                <div class="flex flex-col gap-3.5">
                    <div class="flex gap-3.5 items-start">
                        <div
                            class="w-7 h-7 shrink-0 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-[12px] font-bold">
                            1</div>
                        <div class="flex-1 pt-1">
                            <p class="text-[13px] font-semibold text-slate-950 m-0 mb-0.5">Isi &amp; Kirim Formulir</p>
                            <p class="text-[12px] text-slate-500 leading-relaxed m-0">Lengkapi detail masalah Anda dan
                                kirim tiket.</p>
                        </div>
                    </div>
                    <div class="flex gap-3.5 items-start">
                        <div
                            class="w-7 h-7 shrink-0 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-[12px] font-bold">
                            2</div>
                        <div class="flex-1 pt-1">
                            <p class="text-[13px] font-semibold text-slate-950 m-0 mb-0.5">Tim Menganalisis</p>
                            <p class="text-[12px] text-slate-500 leading-relaxed m-0">Tim ahli kami meninjau dan
                                mengerjakan solusi.</p>
                        </div>
                    </div>
                    <div class="flex gap-3.5 items-start">
                        <div
                            class="w-7 h-7 shrink-0 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center text-[12px] font-bold">
                            3</div>
                        <div class="flex-1 pt-1">
                            <p class="text-[13px] font-semibold text-slate-950 m-0 mb-0.5">Solusi Dikirim</p>
                            <p class="text-[12px] text-slate-500 leading-relaxed m-0">Anda mendapat notifikasi email saat
                                selesai.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-[16px] p-[22px] shadow-[0_2px_1px_rgba(0,0,0,.03),0_4px_16px_rgba(0,0,0,.05)] animate-fade-slide-up delay-180">
                <p class="text-[11px] font-bold text-slate-500 tracking-[0.6px] uppercase m-0 mb-3.5">Tips Tiket Efektif
                </p>
                <div class="flex flex-col gap-2.5">
                    <div class="flex gap-2.5 items-start text-[12.5px] text-slate-800 leading-relaxed">
                        <div class="w-5 h-5 shrink-0 bg-indigo-50 rounded-md flex items-center justify-center mt-px">
                            <svg class="w-[11px] h-[11px] text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        Sertakan pesan error yang muncul secara lengkap
                    </div>
                    <div class="flex gap-2.5 items-start text-[12.5px] text-slate-800 leading-relaxed">
                        <div class="w-5 h-5 shrink-0 bg-indigo-50 rounded-md flex items-center justify-center mt-px">
                            <svg class="w-[11px] h-[11px] text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        Sebutkan browser/OS yang digunakan
                    </div>
                    <div class="flex gap-2.5 items-start text-[12.5px] text-slate-800 leading-relaxed">
                        <div class="w-5 h-5 shrink-0 bg-indigo-50 rounded-md flex items-center justify-center mt-px">
                            <svg class="w-[11px] h-[11px] text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        Lampirkan screenshot jika ada tampilan aneh
                    </div>
                    <div class="flex gap-2.5 items-start text-[12.5px] text-slate-800 leading-relaxed">
                        <div class="w-5 h-5 shrink-0 bg-indigo-50 rounded-md flex items-center justify-center mt-px">
                            <svg class="w-[11px] h-[11px] text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        Jelaskan langkah yang sudah Anda coba
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: @json(session('success'))
                });
            @endif
            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: @json(session('error'))
                });
            @endif
            @if (session('warning'))
                Toast.fire({
                    icon: 'warning',
                    title: @json(session('warning'))
                });
            @endif

            const form = document.getElementById('ticketForm');
            const submitBtn = document.getElementById('submitBtn');
            const descTA = document.getElementById('description');
            const descCount = document.getElementById('descCount');
            const fileInput = document.getElementById('attachment');
            const notif = document.getElementById('fileNotification');

            // ── Submit ──
            form?.addEventListener('submit', function(e) {
                // 1. Validasi reCAPTCHA
                if (typeof grecaptcha === 'undefined' || !grecaptcha.getResponse()) {
                    e.preventDefault();
                    Toast.fire({
                        icon: 'warning',
                        title: 'Mohon selesaikan verifikasi reCAPTCHA!'
                    });
                    return;
                }

                // 2. KUNCI MATIKAN DOUBLE LOADING:
                // Hentikan event agar tidak terbaca oleh listener global NProgress di layouts.user.app
                e.stopImmediatePropagation();

                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg> Mengirim…`;
                }
            });

            // ── Char counter ──
            function updateCounter() {
                if (!descTA) return;
                const len = descTA.value.length;
                descCount.textContent = len.toLocaleString('id-ID');

                // Tailwind text-colors via JS inline style (lebih gampang inject class dinamis)
                descCount.style.color = len >= 5000 ? '#ef4444' : len >= 4500 ? '#f59e0b' : '';
            }
            descTA?.addEventListener('input', updateCounter);
            updateCounter();

            // ── File notif ──
            function showNotif(type, title, sub) {
                const icon = type === 'success' ?
                    `<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>` :
                    `<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`;

                // Assign Tailwind classes instead of custom CSS classes
                notif.className = type === 'success' ?
                    'mt-2.5 text-[13px] rounded-[9px] py-2.5 px-3.5 flex items-center gap-2.5 bg-green-50 text-green-800 border border-green-500/20' :
                    'mt-2.5 text-[13px] rounded-[9px] py-2.5 px-3.5 flex items-center gap-2.5 bg-red-50 text-red-600 border border-red-500/20';

                notif.style.display = 'flex';
                notif.innerHTML = `${icon}<div class="fn-body"></div>`;

                const body = notif.querySelector('.fn-body');
                const strong = document.createElement('strong');
                const span = document.createElement('span');

                strong.className = 'block font-semibold';
                span.className = 'text-[11.5px] opacity-80';
                strong.textContent = title;
                span.textContent = sub;

                body.appendChild(strong);
                body.appendChild(span);

                if (type === 'error') setTimeout(() => notif.style.display = 'none', 5000);
            }

            fileInput?.addEventListener('change', function() {
                const files = this.files;
                if (files.length === 0) {
                    notif.style.display = 'none';
                    return;
                }

                // 1. Validasi Jumlah Maksimal 5
                if (files.length > 5) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Maksimal 5 gambar diperbolehkan!'
                    });
                    showNotif('error', 'Terlalu banyak file',
                        `Anda memilih ${files.length} file, tapi maksimal hanya 5.`);
                    this.value = ''; // Reset input
                    return;
                }

                const allowed = ['jpg', 'jpeg', 'png'];
                let isValid = true;
                let totalSize = 0;

                // 2. Loop untuk cek format & ukuran tiap file
                for (let i = 0; i < files.length; i++) {
                    const ext = files[i].name.split('.').pop().toLowerCase();
                    const sizeMB = files[i].size / 1024 / 1024;

                    if (!allowed.includes(ext)) {
                        showNotif('error', 'Format tidak didukung', `${files[i].name} bukan gambar.`);
                        isValid = false;
                        break;
                    }
                    if (sizeMB > 5) {
                        showNotif('error', 'File terlalu besar', `${files[i].name} melebihi 5MB.`);
                        isValid = false;
                        break;
                    }
                }

                if (isValid) {
                    showNotif('success', `✓ ${files.length} Gambar terpilih`, 'Siap untuk dikirim.');
                    Toast.fire({
                        icon: 'success',
                        title: files.length + ' gambar berhasil dipilih'
                    });
                } else {
                    this.value = ''; // Reset jika ada yang gagal
                }
            });
        });
    </script>

@endsection
