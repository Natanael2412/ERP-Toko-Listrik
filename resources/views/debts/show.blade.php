@extends('layouts.app')
@section('title', 'Detail Utang — ' . $purchase->nomor_faktur)

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <a href="{{ route('debts.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Utang
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">Detail Utang — {{ $purchase->nomor_faktur }}</h1>
    </div>

    {{-- Info Faktur --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Informasi Pembelian</h3>
        <div class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
            <div>
                <p class="text-gray-400">Supplier</p>
                <p class="font-semibold text-gray-800">{{ $purchase->nama_supplier }}</p>
            </div>
            <div>
                <p class="text-gray-400">Produk</p>
                <p class="font-semibold text-gray-800">{{ $purchase->product->nama_barang }}</p>
            </div>
            <div>
                <p class="text-gray-400">Tanggal Beli</p>
                <p class="font-semibold text-gray-800">{{ $purchase->tanggal_masuk->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-gray-400">Total Beli</p>
                <p class="font-semibold text-gray-800">Rp {{ number_format($purchase->total_beli, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-gray-400">Sudah Dibayar</p>
                <p class="font-semibold text-green-600">Rp {{ number_format($purchase->total_beli - $purchase->sisa_hutang, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-gray-400">Sisa Hutang</p>
                <p class="font-bold text-xl {{ $purchase->sisa_hutang > 0 ? 'text-red-600' : 'text-green-600' }}">
                    Rp {{ number_format($purchase->sisa_hutang, 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-gray-400">Jatuh Tempo</p>
                <p class="font-semibold {{ $purchase->isJatuhTempo() ? 'text-red-600' : 'text-gray-800' }}">
                    {{ $purchase->tanggal_jatuh_tempo->format('d/m/Y') }}
                    @if($purchase->isJatuhTempo()) <span class="text-xs">⚠️ Lewat!</span> @endif
                </p>
            </div>
            <div>
                <p class="text-gray-400">Status</p>
                @if($purchase->status_bayar === 'lunas')
                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">Lunas</span>
                @else
                    <span class="inline-flex rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-semibold text-yellow-700">Belum Lunas</span>
                @endif
            </div>
            <div>
                <p class="text-gray-400">Dicatat Oleh</p>
                <p class="font-semibold text-gray-800">{{ $purchase->user->username ?? '-' }}</p>
            </div>
        </div>

        {{-- Progress Bar --}}
        @php $persen = $purchase->total_beli > 0 ? round((($purchase->total_beli - $purchase->sisa_hutang) / $purchase->total_beli) * 100) : 0; @endphp
        <div class="mt-5">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                <span>Progress Pembayaran</span>
                <span class="font-semibold">{{ $persen }}%</span>
            </div>
            <div class="h-2.5 w-full rounded-full bg-gray-200">
                <div class="h-2.5 rounded-full transition-all duration-300 {{ $persen >= 100 ? 'bg-green-500' : 'bg-primary-600' }}" style="width: {{ $persen }}%"></div>
            </div>
        </div>
    </div>

    {{-- Form Bayar (Jika belum lunas) --}}
    @if($purchase->status_bayar === 'belum_lunas')
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">Catat Pembayaran</h3>
        <form method="POST" action="{{ route('debts.pay', $purchase) }}">
            @csrf
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label for="jumlah_bayar" class="mb-1.5 block text-sm font-medium text-gray-700">Jumlah Bayar (Rp)</label>
                    <input type="number" id="jumlah_bayar" name="jumlah_bayar" required min="1" max="{{ $purchase->sisa_hutang }}"
                        value="{{ old('jumlah_bayar', $purchase->sisa_hutang) }}"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('jumlah_bayar') border-red-400 @enderror">
                    @error('jumlah_bayar') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-gray-400">Maks: Rp {{ number_format($purchase->sisa_hutang, 0, ',', '.') }}</p>
                </div>
                <div>
                    <label for="tanggal_bayar" class="mb-1.5 block text-sm font-medium text-gray-700">Tanggal Bayar</label>
                    <input type="date" id="tanggal_bayar" name="tanggal_bayar" value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
                <div>
                    <label for="keterangan" class="mb-1.5 block text-sm font-medium text-gray-700">Keterangan</label>
                    <input type="text" id="keterangan" name="keterangan" value="{{ old('keterangan') }}" placeholder="Opsional..."
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <button type="submit" class="rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow transition hover:bg-primary-700"
                    onclick="return confirm('Catat pembayaran ini?')">
                    Bayar Sekarang
                </button>
                <button type="button" onclick="document.getElementById('jumlah_bayar').value='{{ $purchase->sisa_hutang }}'" 
                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-gray-50">
                    Lunasi Semua
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Riwayat Pembayaran --}}
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-sm font-semibold uppercase tracking-wider text-gray-500">Riwayat Pembayaran</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-gray-100 bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-gray-600">#</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Tanggal</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-600">Jumlah</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Keterangan</th>
                        <th class="px-6 py-3 font-semibold text-gray-600">Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($purchase->payments as $i => $payment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $payment->tanggal_bayar->format('d/m/Y') }}</td>
                        <td class="px-6 py-3 text-right font-semibold text-green-600">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-gray-500 text-xs">{{ $payment->keterangan ?? '-' }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $payment->user->username ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada pembayaran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
