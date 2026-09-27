<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\ReturnItem;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MockDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai generate Mock Data untuk Adit Kejut POS...');

        // 1. Ambil User untuk transaksi
        $owner = User::where('role', 'owner')->first() ?? User::first();
        $kasir = User::where('role', 'kasir')->first() ?? $owner;

        // 2. Buat Kategori
        $kategoriList = ['Kabel', 'Lampu & Pencahayaan', 'Saklar & Stop Kontak', 'MCB & Panel', 'Perkakas Listrik', 'Aksesoris'];
        $categories = [];
        foreach ($kategoriList as $nama) {
            $categories[] = Category::firstOrCreate(['nama_kategori' => $nama]);
        }

        // 3. Buat Produk (sekitar 30 produk)
        $this->command->info('Membuat Produk...');
        $produkList = [
            ['Kabel', 'Kabel Eterna NYM 2x1.5 (Roll 50m)', 350000, 450000],
            ['Kabel', 'Kabel Supreme NYA 1x1.5 (Roll 100m)', 220000, 280000],
            ['Kabel', 'Kabel Eterna NYY 3x2.5 (Meteran)', 12000, 16000],
            ['Kabel', 'Kabel Antena Kitani 20m', 60000, 85000],
            ['Kabel', 'Kabel Serabut Transparan 2x30 (Meteran)', 2000, 3500],
            
            ['Lampu & Pencahayaan', 'Lampu LED Philips 10W Putih', 32000, 42000],
            ['Lampu & Pencahayaan', 'Lampu LED Philips 13W Putih', 40000, 52000],
            ['Lampu & Pencahayaan', 'Lampu Hannochs Sonic 15W', 25000, 35000],
            ['Lampu & Pencahayaan', 'Lampu Sorot LED 50W Outdoor', 80000, 115000],
            ['Lampu & Pencahayaan', 'Lampu Downlight Inbow 5W', 22000, 30000],
            
            ['Saklar & Stop Kontak', 'Saklar Engkel Broco', 11000, 15000],
            ['Saklar & Stop Kontak', 'Saklar Seri (Ganda) Broco', 14000, 19000],
            ['Saklar & Stop Kontak', 'Stop Kontak Broco', 13000, 18000],
            ['Saklar & Stop Kontak', 'Stop Kontak 4 Lubang + Kabel 3m Uticon', 55000, 75000],
            ['Saklar & Stop Kontak', 'Steker Arde Broco', 8000, 12000],
            ['Saklar & Stop Kontak', 'Fitting Plafon Broco', 10000, 14000],
            
            ['MCB & Panel', 'MCB Schneider 1 Phase 2A', 40000, 55000],
            ['MCB & Panel', 'MCB Schneider 1 Phase 4A', 40000, 55000],
            ['MCB & Panel', 'MCB Schneider 1 Phase 6A', 40000, 55000],
            ['MCB & Panel', 'MCB Broco 1 Phase 6A', 25000, 35000],
            ['MCB & Panel', 'Box MCB Isi 4', 15000, 22000],
            
            ['Perkakas Listrik', 'Testpen Bolak Balik Tekiro', 15000, 22000],
            ['Perkakas Listrik', 'Tang Kombinasi Tekiro 8 Inch', 45000, 65000],
            ['Perkakas Listrik', 'Tang Potong Tekiro 6 Inch', 40000, 58000],
            ['Perkakas Listrik', 'Isolasi Listrik Nitto Hitam', 7000, 10000],
            ['Perkakas Listrik', 'Isolasi Listrik Unibel', 3000, 5000],
            
            ['Aksesoris', 'T-Dus Cabang 3', 2500, 4000],
            ['Aksesoris', 'Klem Kabel No. 8', 3000, 5000],
            ['Aksesoris', 'Klem Kabel No. 10', 3500, 6000],
            ['Aksesoris', 'Pipa Conduit 20mm (Batang 3m)', 8000, 12000],
        ];

        $products = [];
        foreach ($produkList as $index => $item) {
            $catId = collect($categories)->firstWhere('nama_kategori', $item[0])->id;
            // Sengaja beri beberapa barang stok menipis (< stok minimum)
            $stok = in_array($index, [5, 12, 20]) ? rand(1, 3) : rand(15, 100); 
            
            $products[] = Product::firstOrCreate(
                ['sku' => 'SKU-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT)],
                [
                    'category_id' => $catId,
                    'nama_barang' => $item[1],
                    'hpp' => $item[2],
                    'harga_jual' => $item[3],
                    'stok' => $stok,
                    'stok_minimum' => 5,
                ]
            );
        }

        // 4. Simulasi Pembelian (Utang Usaha & Pembayaran)
        // Rentang 3 bulan terakhir
        $this->command->info('Membuat data Pembelian & Utang Usaha (3 bulan)...');
        $startDate = Carbon::now()->subMonths(3);
        
        $suppliers = ['PT. Maju Bersama', 'CV. Listrik Jaya', 'Grosir Terang Abadi'];

        for ($i = 1; $i <= 30; $i++) {
            $tglMasuk = $startDate->copy()->addDays(rand(0, 85));
            $tglJatuhTempo = $tglMasuk->copy()->addDays(30); // Tempo 30 hari
            
            $product = $products[array_rand($products)];
            $qty = rand(10, 50);
            $hargaBeli = $product->hpp; 
            $totalBeli = $qty * $hargaBeli;

            // Skenario: 
            // 60% lunas
            // 20% belum lunas (belum jatuh tempo)
            // 20% belum lunas & jatuh tempo (sengaja buat tanggal jatuh tempo di masa lalu)
            $scenario = rand(1, 100);
            
            if ($scenario <= 60) {
                // LUNAS
                $status = 'lunas';
                $sisa = 0;
            } elseif ($scenario <= 80) {
                // BELUM LUNAS (Belum Jatuh Tempo)
                $tglJatuhTempo = Carbon::now()->addDays(rand(5, 15));
                $status = 'belum_lunas';
                $sisa = $totalBeli;
            } else {
                // LEWAT JATUH TEMPO
                $tglJatuhTempo = Carbon::now()->subDays(rand(1, 15));
                $status = 'belum_lunas';
                $sisa = $totalBeli;
            }

            $purchase = Purchase::create([
                'user_id' => $owner->id,
                'product_id' => $product->id,
                'nomor_faktur' => 'INV-SUP-' . $tglMasuk->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_supplier' => $suppliers[array_rand($suppliers)],
                'qty_masuk' => $qty,
                'harga_beli' => $hargaBeli,
                'total_beli' => $totalBeli,
                'tanggal_masuk' => $tglMasuk,
                'tanggal_jatuh_tempo' => $tglJatuhTempo,
                'status_bayar' => $status,
                'sisa_hutang' => $sisa,
            ]);

            // Kalau Lunas, buat record payment
            if ($status === 'lunas') {
                PurchasePayment::create([
                    'purchase_id' => $purchase->id,
                    'user_id' => $owner->id,
                    'jumlah_bayar' => $totalBeli,
                    'tanggal_bayar' => $tglMasuk->copy()->addDays(rand(1, 15)),
                    'keterangan' => 'Pelunasan transfer',
                ]);
            }
        }

        // 5. Simulasi Penjualan (Ramai: ~8-15 transaksi per hari selama 90 hari)
        $this->command->info('Membuat data Penjualan (Ramai, ~10 trx/hari selama 90 hari)...');
        $transaksiTertentuId = null; // untuk keperluan retur nanti

        for ($d = 0; $d <= 90; $d++) {
            $tglTrx = $startDate->copy()->addDays($d);
            $jmlTrxHariIni = rand(8, 18); 

            for ($t = 0; $t < $jmlTrxHariIni; $t++) {
                // Waktu random di hari tersebut antara jam 08:00 - 20:00
                $waktuTrx = $tglTrx->copy()->setTime(rand(8, 19), rand(0, 59), rand(0, 59));
                
                $trx = Transaction::create([
                    'user_id' => $kasir->id,
                    'nomor_nota' => 'POS-' . $waktuTrx->format('ymdHi') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'tanggal_waktu' => $waktuTrx,
                    'total_hpp' => 0, // akan diupdate
                    'subtotal' => 0,
                    'diskon' => 0,
                    'total_penjualan' => 0, // akan diupdate
                ]);

                // Item dibeli (1 - 4 jenis barang)
                $jmlItem = rand(1, 4);
                $totalHpp = 0;
                $totalJual = 0;

                for ($j = 0; $j < $jmlItem; $j++) {
                    $p = $products[array_rand($products)];
                    $qtyBeli = rand(1, 5);
                    $subtotalJual = $p->harga_jual * $qtyBeli;
                    $subtotalHpp = $p->hpp * $qtyBeli;

                    $td = TransactionDetail::create([
                        'transaction_id' => $trx->id,
                        'product_id' => $p->id,
                        'qty' => $qtyBeli,
                        'harga_satuan' => $p->harga_jual,
                        'subtotal' => $subtotalJual,
                    ]);

                    $totalHpp += $subtotalHpp;
                    $totalJual += $subtotalJual;
                    
                    // Simpan 1 ID detail transaksi untuk diskenariokan Retur
                    if (!$transaksiTertentuId && $d > 80 && $qtyBeli >= 2) {
                        $transaksiTertentuId = $td->id;
                    }
                }

                $trx->update([
                    'total_hpp' => $totalHpp,
                    'subtotal' => $totalJual,
                    'total_penjualan' => $totalJual,
                ]);
            }
        }

        // 6. Skenario Retur Barang
        $this->command->info('Membuat skenario Retur Barang...');
        if ($transaksiTertentuId) {
            $td = TransactionDetail::find($transaksiTertentuId);
            // Retur 1 barang
            DB::table('returns')->insert([
                'transaction_detail_id' => $td->id,
                'user_id' => $owner->id,
                'qty_retur' => 1,
                'alasan' => 'Barang rusak dari pabrik',
                'jumlah_refund' => $td->harga_satuan,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ]);
        }
        
        // Buat beberapa retur tambahan acak di bulan lalu
        for ($r=0; $r<5; $r++) {
            $randomTd = TransactionDetail::inRandomOrder()->first();
            if ($randomTd && $randomTd->qty > 1) {
                DB::table('returns')->insert([
                    'transaction_detail_id' => $randomTd->id,
                    'user_id' => $owner->id,
                    'qty_retur' => 1,
                    'alasan' => 'Customer salah beli ukuran',
                    'jumlah_refund' => $randomTd->harga_satuan,
                    'created_at' => Carbon::now()->subDays(rand(10, 40)),
                    'updated_at' => Carbon::now()->subDays(rand(10, 40)),
                ]);
            }
        }

        $this->command->info('✅ Generate Mock Data selesai! Silakan cek dashboard.');
    }
}
