@extends('layouts.app')
@section('title', 'Detail Transaksi - ' . $transaction->nomor_nota)

@section('content')
<div class="max-w-3xl mx-auto pb-12">
    {{-- Tombol Aksi (Hanya Tampil di Layar) --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 print:hidden">
        <a href="{{ route('pos.history') }}" class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Riwayat
        </a>
        <button onclick="window.print()" class="flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary-600/30 transition hover:bg-primary-700 active:scale-[0.98]">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Struk / PDF
        </button>
    </div>

    {{-- Kertas Invoice --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm print:rounded-none print:border-none print:shadow-none">
        <div class="p-8 sm:p-12">
            
            {{-- Header Invoice --}}
            <div class="flex flex-col-reverse justify-between gap-6 sm:flex-row sm:items-start">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">ADIT KEJUT</h1>
                    <p class="mt-2 text-sm text-gray-500 max-w-xs">Jl. Contoh Toko Listrik No. 123<br>Kota Anda, Provinsi Anda 12345<br>Telp: 0812-3456-7890</p>
                </div>
                <div class="text-left sm:text-right">
                    <h2 class="text-2xl font-bold uppercase tracking-widest text-primary-600">INVOICE</h2>
                    <p class="mt-1 font-mono text-sm font-medium text-gray-800">{{ $transaction->nomor_nota }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $transaction->tanggal_waktu->format('d/m/Y H:i') }}</p>
                    <span class="mt-3 inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                        {{ strtoupper($transaction->metode_pembayaran ?? 'CASH') }}
                    </span>
                </div>
            </div>

            <hr class="my-8 border-gray-200 border-dashed">

            {{-- Info Kasir & Pembeli --}}
            <div class="flex flex-col gap-6 sm:flex-row sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Kasir Bertugas</p>
                    <p class="mt-1 font-medium text-gray-800">{{ $transaction->user->username }}</p>
                </div>
                <div class="sm:text-right">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Pelanggan</p>
                    <p class="mt-1 font-medium text-gray-800">Pelanggan Umum</p>
                </div>
            </div>

            {{-- Tabel Item --}}
            <div class="mt-10 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-800 text-gray-800">
                            <th class="py-3 font-semibold">Deskripsi Produk</th>
                            <th class="py-3 text-right font-semibold">Harga</th>
                            <th class="py-3 text-right font-semibold">Qty</th>
                            <th class="py-3 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaction->details as $detail)
                        <tr>
                            <td class="py-4">
                                <p class="font-medium text-gray-900">{{ $detail->product->nama_barang }}</p>
                                <p class="font-mono text-xs text-gray-400">{{ $detail->product->sku }}</p>
                            </td>
                            <td class="py-4 text-right text-gray-600">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-4 text-right text-gray-600">{{ $detail->qty }} {{ $detail->product->satuan ?? 'pcs' }}</td>
                            <td class="py-4 text-right font-medium text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Ringkasan Total --}}
            <div class="mt-8 flex justify-end">
                <div class="w-full max-w-sm space-y-3 rounded-xl bg-gray-50 p-6 print:bg-transparent print:p-0">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium text-gray-800">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($transaction->diskon > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Diskon</span>
                        <span class="font-medium text-red-500">-Rp {{ number_format($transaction->diskon, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="border-t border-gray-200 pt-3">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-800">TOTAL</span>
                            <span class="text-2xl font-extrabold text-primary-600 print:text-gray-900">Rp {{ number_format($transaction->total_penjualan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Note --}}
            <div class="mt-16 text-center text-sm text-gray-400 print:mt-12">
                <p>Terima kasih atas kepercayaan Anda berbelanja di Adit Kejut.</p>
                <p>Barang yang sudah dibeli tidak dapat dikembalikan tanpa nota ini.</p>
            </div>
        </div>
    </div>
</div>
@endsection
