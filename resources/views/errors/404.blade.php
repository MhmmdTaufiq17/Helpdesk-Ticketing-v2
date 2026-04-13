{{-- resources/views/errors/404.blade.php --}}
@extends('layouts.user.app')

@section('title', '404 - Halaman Tidak Ditemukan')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="text-center max-w-md mx-auto">
        {{-- Ilustrasi / Icon --}}
        <div class="mb-8">
            <div class="w-32 h-32 mx-auto bg-gradient-to-br from-[#5b5ef4]/10 to-indigo-500/10 rounded-full flex items-center justify-center">
                <svg class="w-16 h-16 text-[#5b5ef4]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        {{-- 404 Text --}}
        <h1 class="text-8xl font-black text-[#5b5ef4] mb-4">404</h1>

        <h2 class="text-2xl font-bold text-slate-800 mb-3">
            Halaman Tidak Ditemukan
        </h2>

        <p class="text-slate-500 mb-8">
            Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan.
        </p>

        {{-- Buttons --}}
        <div class="space-y-3">
            <a href="{{ route('user.home') }}"
                class="inline-flex items-center justify-center w-full gap-2 px-6 py-3 bg-[#5b5ef4] text-white rounded-xl font-semibold hover:bg-indigo-600 transition-all shadow-lg shadow-indigo-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Kembali ke Beranda
            </a>

            <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('user.home') }}"
                class="inline-flex items-center justify-center w-full gap-2 px-6 py-3 bg-white border border-slate-200 rounded-xl text-slate-700 font-semibold hover:bg-slate-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Halaman Sebelumnya
            </a>
        </div>
    </div>
</div>
@endsection
