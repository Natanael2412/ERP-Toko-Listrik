@extends('layouts.app')
@section('title', 'Laporan Neraca')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Neraca (Balance Sheet)</h1>
        <p class="text-sm text-gray-500">Melihat posisi keuangan toko pada tanggal tertentu.</p>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('reports.balance-sheet') }}" class="flex flex-wrap items-end gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div>
            <label for="tanggal" class="mb-1 block text-xs font-medium text-gray-500">Per Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" value="{{ $tanggal }}" class="block w-40 rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
        </div>
        <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-700">
            Tampilkan
        </button>
    </form>

    {{-- Neraca Side-by-Side --}}
    <div class="flex flex-col md:flex-row gap-6">
        {{-- ASET --}}
        <div class="flex-1 rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="bg-blue-50 px-5 py-4 border-b border-blue-100">
                <h3 class="text-base font-bold text-blue-800">Aset (Aktiva)</h3>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span class="text-sm text-gray-600">Kas & Bank</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-800">Rp {{ number_format($kas, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span class="text-sm text-gray-600">Persediaan Barang</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-800">Rp {{ number_format($persediaan, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between items-center pt-3 mt-2 border-t-2 border-blue-600">
                    <span class="text-base font-bold text-gray-800">Total Aset</span>
                    <span class="text-lg font-bold text-blue-600">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- KEWAJIBAN & EKUITAS --}}
        <div class="flex-1 rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="bg-green-50 px-5 py-4 border-b border-green-100">
                <h3 class="text-base font-bold text-green-800">Kewajiban & Ekuitas (Pasiva)</h3>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm text-gray-600">Hutang Usaha</span>
                    </div>
                    <span class="text-sm font-semibold text-red-600">Rp {{ number_format($hutangUsaha, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm text-gray-600">Modal (Ekuitas)</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-800">Rp {{ number_format($modal, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between items-center pt-3 mt-2 border-t-2 border-green-600">
                    <span class="text-base font-bold text-gray-800">Total Pasiva</span>
                    <span class="text-lg font-bold text-green-600">Rp {{ number_format($hutangUsaha + $modal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Keterangan --}}
    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-sm text-xs text-gray-500">
        <p><strong>Catatan:</strong> Neraca ini bersifat <em>sederhana</em> untuk toko retail kecil. Kas dihitung dari total penjualan dikurangi pembelian lunas. Persediaan dihitung dari stok × harga beli terakhir.</p>
    </div>
</div>
@endsection
