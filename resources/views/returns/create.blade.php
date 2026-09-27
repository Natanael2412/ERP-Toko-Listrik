@extends('layouts.app')
@section('title', 'Buat Retur')

@section('content')
<div class="mx-auto max-w-2xl space-y-6">
    <div>
        <a href="{{ route('returns.index') }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Retur
        </a>
        <h1 class="mt-2 text-2xl font-bold text-gray-800">Buat Retur Penjualan</h1>
    </div>

    {{-- Step 1: Cari Nota --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="mb-4 font-semibold text-gray-800">1. Cari Nomor Nota</h3>
        <form method="GET" action="{{ route('returns.create') }}" class="flex gap-3">
            <input type="text" name="nomor_nota" value="{{ $nomorNota ?? '' }}" required placeholder="Masukkan nomor nota (INV-...)"
                class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-gray-700">Cari</button>
        </form>
    </div>

    {{-- Step 2: Detail Transaksi & Form Retur --}}
    @if(isset($transaction) && $transaction)
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h3 class="mb-2 font-semibold text-gray-800">2. Detail Transaksi</h3>
        <div class="mb-4 flex gap-4 text-sm text-gray-600">
            <span>Nota: <span class="font-semibold text-gray-800">{{ $transaction->nomor_nota }}</span></span>
            <span>Tanggal: {{ $transaction->tanggal_waktu->format('d/m/Y H:i') }}</span>
            <span>Kasir: {{ $transaction->user->username ?? '-' }}</span>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 font-semibold text-gray-600">Produk</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-600">Qty Beli</th>
                        <th class="px-4 py-2 text-right font-semibold text-gray-600">Harga</th>
                        <th class="px-4 py-2 text-center font-semibold text-gray-600">Retur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($transaction->details as $detail)
                    @php
                        $sudahDiretur = $detail->returns ? $detail->returns->sum('qty_retur') : \App\Models\ReturnItem::where('transaction_detail_id', $detail->id)->sum('qty_retur');
                        $sisaRetur = $detail->qty - $sudahDiretur;
                    @endphp
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $detail->product->nama_barang }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $detail->qty }}
                            @if($sudahDiretur > 0)
                                <span class="text-xs text-red-500">(diretur: {{ $sudahDiretur }})</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($sisaRetur > 0)
                            <form method="POST" action="{{ route('returns.store') }}" class="inline-flex items-center gap-2" onsubmit="return confirm('Proses retur untuk {{ $detail->product->nama_barang }}?')">
                                @csrf
                                <input type="hidden" name="transaction_detail_id" value="{{ $detail->id }}">
                                <input type="number" name="qty_retur" min="1" max="{{ $sisaRetur }}" value="1" required
                                    class="w-16 rounded-lg border border-gray-300 px-2 py-1 text-center text-sm focus:border-primary-500 focus:outline-none">
                                <input type="text" name="alasan" placeholder="Alasan retur" required
                                    class="w-32 rounded-lg border border-gray-300 px-2 py-1 text-sm focus:border-primary-500 focus:outline-none">
                                <button type="submit" class="rounded-lg bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700">Retur</button>
                            </form>
                            @else
                            <span class="text-xs text-gray-400">Sudah full retur</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif(isset($nomorNota) && $nomorNota)
    <div class="rounded-xl border border-red-200 bg-red-50 p-6 text-center text-sm text-red-600">
        Nomor nota <strong>{{ $nomorNota }}</strong> tidak ditemukan.
    </div>
    @endif
</div>
@endsection
