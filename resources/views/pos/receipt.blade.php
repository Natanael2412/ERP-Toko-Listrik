<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk — {{ $transaction->nomor_nota }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; color: #333; max-width: 300px; margin: 0 auto; padding: 20px 10px; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .line { border-top: 1px dashed #999; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; padding: 2px 0; }
        .item-name { margin-bottom: 2px; }
        .item-detail { color: #666; font-size: 11px; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .footer { margin-top: 16px; font-size: 11px; color: #888; }
        @media print {
            body { margin: 0; padding: 5px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="center">
        <h1>⚡ ADIT KEJUT</h1>
        <p>Sistem POS</p>
        <div class="line"></div>
        <div class="row">
            <span>No. Nota:</span>
            <span class="bold">{{ $transaction->nomor_nota }}</span>
        </div>
        <div class="row">
            <span>Tanggal:</span>
            <span>{{ $transaction->tanggal_waktu->format('d/m/Y H:i') }}</span>
        </div>
        <div class="row">
            <span>Kasir:</span>
            <span>{{ $transaction->user->username }}</span>
        </div>
    </div>

    <div class="line"></div>

    {{-- Detail Item --}}
    @foreach($transaction->details as $detail)
    <div style="margin-bottom: 6px;">
        <div class="item-name bold">{{ $detail->product->nama_barang }}</div>
        <div class="row item-detail">
            <span>{{ $detail->qty }} x Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</span>
            <span>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
        </div>
    </div>
    @endforeach

    <div class="line"></div>

    {{-- Ringkasan --}}
    <div class="row">
        <span>Subtotal</span>
        <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
    </div>
    @if($transaction->diskon > 0)
    <div class="row">
        <span>Diskon</span>
        <span>-Rp {{ number_format($transaction->diskon, 0, ',', '.') }}</span>
    </div>
    @endif
    <div class="line"></div>
    <div class="row bold" style="font-size: 14px;">
        <span>TOTAL</span>
        <span>Rp {{ number_format($transaction->total_penjualan, 0, ',', '.') }}</span>
    </div>

    <div class="line"></div>

    <div class="center footer">
        <p>Terima kasih atas kunjungan Anda!</p>
        <p>Barang yang sudah dibeli tidak dapat</p>
        <p>dikembalikan tanpa nota ini.</p>
    </div>

    {{-- Tombol Print & Kembali --}}
    <div class="center no-print" style="margin-top: 24px;">
        <button onclick="window.print()" style="background: #2563eb; color: white; border: none; padding: 10px 24px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: bold;">
            🖨️ Cetak Struk
        </button>
        <br><br>
        <a href="{{ route('pos.index') }}" style="color: #2563eb; text-decoration: none; font-size: 13px;">← Transaksi Baru</a>
        &nbsp;|&nbsp;
        <a href="{{ route('pos.history') }}" style="color: #666; text-decoration: none; font-size: 13px;">Riwayat</a>
    </div>
</body>
</html>
