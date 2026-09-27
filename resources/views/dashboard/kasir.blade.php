@extends('layouts.app')
@section('title', 'Dashboard Kasir')

@section('content')
<div class="space-y-6">
    {{-- Greeting --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Halo, {{ auth()->user()->username }}! 👋</h1>
        <p class="text-sm text-gray-500">Ringkasan transaksi Anda hari ini — {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Kartu Statistik --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        {{-- Jumlah Transaksi --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $transaksiHariIni }}</p>
                </div>
            </div>
        </div>

        {{-- Total Penjualan --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Penjualan</p>
                    <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalPenjualanHariIni, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Action --}}
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-6">
        <h3 class="mb-2 font-semibold text-blue-800">Mulai Transaksi Baru</h3>
        <p class="mb-4 text-sm text-blue-600">Klik tombol di bawah untuk memulai transaksi penjualan baru.</p>
        <a href="#" class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Transaksi Baru
        </a>
    </div>
</div>
@endsection
