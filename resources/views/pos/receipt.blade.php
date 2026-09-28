@extends('layouts.app')
@section('title', 'Detail Transaksi - ' . $transaction->nomor_nota)

@section('content')
<div class="max-w-5xl mx-auto pb-12 px-4 sm:px-6 lg:px-8 pt-6">
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
    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-xl shadow-gray-200/40 print:rounded-none print:border-none print:shadow-none">
        <div class="p-10 sm:p-16 lg:p-24">
            
            {{-- Header Invoice --}}
            <div class="flex flex-col-reverse justify-between gap-12 sm:flex-row sm:items-start">
                <div>
                    <h1 class="text-4xl font-black text-gray-900 tracking-tighter">ADIT KEJUT</h1>
                    <p class="mt-4 text-sm leading-loose text-gray-500 max-w-sm">
                        Jl. Contoh Toko Listrik No. 123<br>
                        Kota Anda, Provinsi Anda 12345<br>
                        Telp: 0812-3456-7890
                    </p>
                </div>
                <div class="text-left sm:text-right">
                    <h2 class="text-3xl font-black uppercase tracking-widest text-primary-600">INVOICE</h2>
                    <p class="mt-4 font-mono text-base font-medium text-gray-800">{{ $transaction->nomor_nota }}</p>
                    <p class="mt-2 text-sm text-gray-500">{{ $transaction->tanggal_waktu->format('d/m/Y H:i') }}</p>
                    <span class="mt-4 inline-flex items-center rounded-full bg-green-50 px-4 py-1.5 text-xs font-bold tracking-widest text-green-700 uppercase">
                        {{ $transaction->metode_pembayaran ?? 'CASH' }}
                    </span>
                </div>
            </div>

            <hr class="my-16 border-gray-100 border-dashed">

            {{-- Info Kasir & Pembeli --}}
            <div class="flex flex-col gap-10 sm:flex-row sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Kasir Bertugas</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $transaction->user->username }}</p>
                </div>
                <div class="sm:text-right">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Pelanggan</p>
                    <p class="text-lg font-semibold text-gray-900">Pelanggan Umum</p>
                </div>
            </div>

            {{-- Tabel Item --}}
            <div class="mt-20 overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-gray-900 text-gray-900">
                            <th class="py-5 font-bold uppercase tracking-wider text-xs">Deskripsi Produk</th>
                            <th class="py-5 text-right font-bold uppercase tracking-wider text-xs">Harga</th>
                            <th class="py-5 text-right font-bold uppercase tracking-wider text-xs">Qty</th>
                            <th class="py-5 text-right font-bold uppercase tracking-wider text-xs">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaction->details as $detail)
                        <tr>
                            <td class="py-8">
                                <p class="text-base font-semibold text-gray-900 mb-1">{{ $detail->product->nama_barang }}</p>
                                <p class="font-mono text-xs text-gray-400">{{ $detail->product->sku }}</p>
                            </td>
                            <td class="py-8 text-right text-gray-600">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-8 text-right text-gray-600">{{ $detail->qty }} {{ $detail->product->satuan ?? 'pcs' }}</td>
                            <td class="py-8 text-right font-semibold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Ringkasan Total --}}
            <div class="mt-16 flex justify-end">
                <div class="w-full max-w-md space-y-5 rounded-2xl bg-gray-50/50 p-10 print:bg-transparent print:p-0">
                    <div class="flex justify-between text-base">
                        <span class="text-gray-500 font-medium">Subtotal</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($transaction->subtotal ?: $transaction->total_penjualan, 0, ',', '.') }}</span>
                    </div>
                    @if($transaction->diskon > 0)
                    <div class="flex justify-between text-base">
                        <span class="text-gray-500 font-medium">Diskon</span>
                        <span class="font-semibold text-red-500">-Rp {{ number_format($transaction->diskon, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <div class="flex justify-between items-end">
                            <span class="font-black text-gray-900 tracking-wider">TOTAL</span>
                            <span class="text-3xl font-black text-primary-600 print:text-gray-900">Rp {{ number_format($transaction->total_penjualan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Note --}}
            <div class="mt-32 text-center text-sm text-gray-400 print:mt-24 space-y-2">
                <p class="font-medium">Terima kasih atas kepercayaan Anda berbelanja di Adit Kejut.</p>
                <p>Barang yang sudah dibeli tidak dapat dikembalikan tanpa nota ini.</p>
            </div>
        </div>
    </div>
</div>
@endsection
