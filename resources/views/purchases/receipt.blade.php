@extends('layouts.app')
@section('title', 'Detail Faktur Pembelian - ' . $purchase->nomor_faktur)

@section('content')
<div class="max-w-3xl mx-auto pb-12">
    {{-- Tombol Aksi (Hanya Tampil di Layar) --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4 print:hidden">
        <a href="{{ route('purchases.index') }}" class="flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Pembelian
        </a>
        <button onclick="window.print()" class="flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-primary-600/30 transition hover:bg-primary-700 active:scale-[0.98]">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Faktur Internal
        </button>
    </div>

    {{-- Kertas Faktur --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm print:rounded-none print:border-none print:shadow-none">
        <div class="p-8 sm:p-12">
            
            {{-- Header Faktur --}}
            <div class="flex flex-col-reverse justify-between gap-6 sm:flex-row sm:items-start">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">ADIT KEJUT</h1>
                    <p class="mt-2 text-sm font-semibold text-gray-500">Pencatatan Internal Toko</p>
                </div>
                <div class="text-left sm:text-right">
                    <h2 class="text-2xl font-bold uppercase tracking-widest text-primary-600">FAKTUR BELI</h2>
                    <p class="mt-1 font-mono text-sm font-medium text-gray-800">{{ $purchase->nomor_faktur }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $purchase->tanggal_masuk->format('d/m/Y') }}</p>
                    <span class="mt-3 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $purchase->status_bayar === 'lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        STATUS: {{ strtoupper($purchase->status_bayar) }}
                    </span>
                </div>
            </div>

            <hr class="my-8 border-gray-200 border-dashed">

            {{-- Info Supplier & Pembeli --}}
            <div class="flex flex-col gap-6 sm:flex-row sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Penerima Barang</p>
                    <p class="mt-1 font-medium text-gray-800">Toko Adit Kejut</p>
                    <p class="text-sm text-gray-500">Admin Bertugas: {{ auth()->user()->username ?? 'Admin' }}</p>
                </div>
                <div class="sm:text-right">
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Supplier (Pemasok)</p>
                    <p class="mt-1 font-medium text-gray-800">{{ $purchase->supplier->nama_supplier ?? '-' }}</p>
                    @if($purchase->supplier && $purchase->supplier->kontak_supplier)
                        <p class="text-sm text-gray-500">{{ $purchase->supplier->kontak_supplier }}</p>
                    @endif
                </div>
            </div>

            {{-- Tabel Item --}}
            <div class="mt-10 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-800 text-gray-800">
                            <th class="py-3 font-semibold">Deskripsi Produk</th>
                            <th class="py-3 text-right font-semibold">Harga Beli</th>
                            <th class="py-3 text-right font-semibold">Qty</th>
                            <th class="py-3 text-right font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-4">
                                <p class="font-medium text-gray-900">{{ $purchase->product->nama_barang }}</p>
                                <p class="font-mono text-xs text-gray-400">{{ $purchase->product->sku }}</p>
                            </td>
                            <td class="py-4 text-right text-gray-600">Rp {{ number_format($purchase->harga_beli, 0, ',', '.') }}</td>
                            <td class="py-4 text-right text-gray-600">{{ $purchase->qty_masuk }} {{ $purchase->product->satuan ?? 'pcs' }}</td>
                            <td class="py-4 text-right font-medium text-gray-900">Rp {{ number_format($purchase->total_beli, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($purchase->bukti_faktur)
            <div class="mt-8 border-t border-gray-200 pt-8 print:hidden">
                <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-gray-400">Bukti Faktur (Scan / Foto)</h3>
                <div class="overflow-hidden rounded-xl border border-gray-200">
                    <img src="{{ asset('storage/' . $purchase->bukti_faktur) }}" alt="Bukti Faktur {{ $purchase->nomor_faktur }}" class="w-full object-contain max-h-[600px]">
                </div>
            </div>
            @endif

            {{-- Ringkasan Total --}}
            <div class="mt-8 flex justify-end">
                <div class="w-full max-w-sm space-y-3 rounded-xl bg-gray-50 p-6 print:bg-transparent print:p-0">
                    <div class="flex justify-between items-center border-b border-gray-200 pb-3">
                        <span class="font-bold text-gray-800">TOTAL BELI</span>
                        <span class="text-xl font-extrabold text-primary-600 print:text-gray-900">Rp {{ number_format($purchase->total_beli, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Sisa Hutang</span>
                        <span class="font-medium text-red-600">Rp {{ number_format($purchase->sisa_hutang, 0, ',', '.') }}</span>
                    </div>

                    @if($purchase->status_bayar !== 'lunas')
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Jatuh Tempo</span>
                        <span class="font-medium text-gray-800">{{ $purchase->tanggal_jatuh_tempo->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Footer Note --}}
            <div class="mt-16 text-center text-sm text-gray-400 print:mt-12">
                <p>Dokumen ini adalah bukti pencatatan stok dan hutang internal Toko Adit Kejut.</p>
                <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
