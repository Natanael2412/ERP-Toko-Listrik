<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\ReturnItem;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. BUAT USERS
        // ==========================================
        $admin = User::create(['username' => 'admin', 'email' => 'admin@tokolistrik.com', 'password' => 'password', 'role' => 'admin']);
        $owner = User::create(['username' => 'owner', 'email' => 'owner@tokolistrik.com', 'password' => 'password', 'role' => 'owner']);
        $kasir = User::create(['username' => 'kasir', 'email' => 'kasir@tokolistrik.com', 'password' => 'password', 'role' => 'kasir']);

        // ==========================================
        // 2. KATEGORI & SUPPLIER
        // ==========================================
        $kategoriList = [
            'Kabel & Kawat', 'Lampu & Penerangan', 'Saklar & Stop Kontak', 
            'MCB & Pengaman', 'Alat-alat Listrik', 'Aksesoris Listrik', 
            'Pipa & Conduit', 'Komponen Panel', 'Baterai & Aki', 
            'Kipas & Ventilasi', 'Antena & Komunikasi', 'Smart Home Devices'
        ];
        $categories = [];
        foreach ($kategoriList as $nama) {
            $categories[] = Category::create(['nama_kategori' => $nama]);
        }

        $supplierList = [
            ['nama_supplier' => 'PT Philips Indonesia', 'kontak' => '081234567890', 'alamat' => 'Jakarta Selatan'],
            ['nama_supplier' => 'PT Supreme Cable', 'kontak' => '081987654321', 'alamat' => 'Tangerang'],
            ['nama_supplier' => 'PT Broco Electrical', 'kontak' => '081122334455', 'alamat' => 'Bekasi'],
            ['nama_supplier' => 'PT Schneider Electric', 'kontak' => '082233445566', 'alamat' => 'Jakarta Timur'],
            ['nama_supplier' => 'PT Panasonic Gobel', 'kontak' => '081199887766', 'alamat' => 'Cawang'],
            ['nama_supplier' => 'PT Eterna Cable', 'kontak' => '081288889999', 'alamat' => 'Cikarang'],
            ['nama_supplier' => 'PT Hannochs', 'kontak' => '081377776666', 'alamat' => 'Medan'],
            ['nama_supplier' => 'PT Uticon Electrical', 'kontak' => '085566778899', 'alamat' => 'Semarang']
        ];
        $suppliers = [];
        foreach ($supplierList as $sup) {
            $suppliers[] = Supplier::create($sup);
        }

        // ==========================================
        // 3. PRODUK (Generate 120 Products)
        // ==========================================
        $products = [];
        $supMap = [];
        
        $satuanOptions = ['Pcs', 'Roll', 'Meter', 'Dus', 'Lusin', 'Set'];

        for ($i = 1; $i <= 120; $i++) {
            $cat = $categories[array_rand($categories)];
            $sup = $suppliers[array_rand($suppliers)];
            
            $hpp = rand(10, 500) * 1000; 
            $harga_jual = (int) ($hpp * (rand(130, 180) / 100)); // Profit margin 30% - 80%

            $p = Product::create([
                'category_id' => $cat->id,
                'sku' => Product::generateSKU($cat->id),
                'nama_barang' => 'Produk Listrik ' . $cat->nama_kategori . ' Tipe ' . rand(100, 999) . ' - ' . Str::random(3),
                'satuan' => $satuanOptions[array_rand($satuanOptions)],
                'stok' => 0,
                'stok_minimum' => rand(5, 20),
                'hpp' => $hpp,
                'harga_jual' => $harga_jual
            ]);

            $products[] = $p;
            $supMap[$p->id] = $sup;
        }

        // Disable event auditing temporally to speed up seeding
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // ==========================================
        // 4. PEMBELIAN (Jan 2026 - Sep 2026)
        // ==========================================
        $startDate = Carbon::create(2026, 1, 1);
        $endDate = Carbon::now();
        $currentDate = clone $startDate;

        $fakturCount = 1;
        while ($currentDate <= $endDate) {
            // Setiap ~5-10 hari ada restock untuk beberapa barang
            if (rand(1, 100) <= 20) { // 20% chance per day to restock
                $numItemsToRestock = rand(1, 4);
                $keys = array_rand($products, $numItemsToRestock);
                $keys = is_array($keys) ? $keys : [$keys];

                foreach ($keys as $k) {
                    $p = $products[$k];
                    $qty = rand(20, 100);
                    $totalHarga = $qty * $p->hpp;
                    
                    // Lunas atau Ngutang
                    // Jika bulan lama, kebanyakan lunas. Jika baru-baru ini, mungkin belum lunas.
                    $isHutang = rand(1, 100) <= 30; // 30% hutang
                    $jatuhTempo = clone $currentDate;
                    $jatuhTempo->addDays(30);

                    Purchase::create([
                        'user_id' => $admin->id,
                        'supplier_id' => $supMap[$p->id]->id,
                        'nama_supplier' => $supMap[$p->id]->nama_supplier,
                        'product_id' => $p->id,
                        'nomor_faktur' => 'INV-SUP-' . str_pad($fakturCount++, 4, '0', STR_PAD_LEFT),
                        'qty_masuk' => $qty,
                        'harga_beli' => $p->hpp,
                        'total_beli' => $totalHarga,
                        'tanggal_masuk' => $currentDate,
                        'tanggal_jatuh_tempo' => $jatuhTempo,
                        'status_bayar' => $isHutang ? 'belum_lunas' : 'lunas',
                        'sisa_hutang' => $isHutang ? $totalHarga : 0,
                    ]);

                    // Tambah stok
                    $p->increment('stok', $qty);
                }
            }
            
            // ==========================================
            // 5. TRANSAKSI (Penjualan Harian)
            // ==========================================
            // Sekitar 1-5 transaksi per hari
            $numTransactions = rand(1, 5);
            for ($i = 0; $i < $numTransactions; $i++) {
                $waktuTx = clone $currentDate;
                $waktuTx->addHours(rand(8, 17))->addMinutes(rand(0, 59));

                static $dailySequence = [];
                $dateKey = $waktuTx->format('Ymd');
                if (!isset($dailySequence[$dateKey])) {
                    $dailySequence[$dateKey] = 1;
                }
                $seqStr = str_pad($dailySequence[$dateKey]++, 4, '0', STR_PAD_LEFT);

                $trx = Transaction::create([
                    'nomor_nota' => 'INV-' . $dateKey . '-' . $seqStr,
                    'user_id' => $kasir->id,
                    'tanggal_waktu' => $waktuTx,
                    'total_penjualan' => 0,
                    'total_hpp' => 0,
                    'metode_pembayaran' => rand(1, 10) > 2 ? 'cash' : 'transfer', // 80% cash
                    'status' => 'sukses',
                ]);

                $totalJual = 0;
                $totalHpp = 0;

                // 1-3 item per transaksi
                $numItems = rand(1, 3);
                $keys = array_rand($products, $numItems);
                $keys = is_array($keys) ? $keys : [$keys];

                foreach ($keys as $k) {
                    $p = $products[$k];
                    // Pastikan stok ada
                    if ($p->stok > 0) {
                        $qty = rand(1, min(5, $p->stok));
                        $subJual = $qty * $p->harga_jual;
                        $subHpp = $qty * $p->hpp;

                        TransactionDetail::create([
                            'transaction_id' => $trx->id,
                            'product_id' => $p->id,
                            'qty' => $qty,
                            'harga_satuan' => $p->harga_jual,
                            'subtotal' => $subJual,
                        ]);

                        $totalJual += $subJual;
                        $totalHpp += $subHpp;

                        $p->decrement('stok', $qty);
                    }
                }

                if ($totalJual == 0) {
                    $trx->delete();
                } else {
                    $diskonNominal = 0;
                    $tipeDiskon = 'nominal';
                    
                    // 25% chance of discount
                    if (rand(1, 100) <= 25) {
                        if (rand(0, 1) == 0) {
                            // Diskon Persen (5% sampai 20%)
                            $persen = rand(1, 4) * 5;
                            $tipeDiskon = 'persen';
                            $diskonNominal = $totalJual * ($persen / 100);
                        } else {
                            // Diskon Nominal (Rp 5.000 sampai 50.000)
                            $pilihanDiskon = [5000, 10000, 20000, 50000];
                            $diskonNominal = $pilihanDiskon[array_rand($pilihanDiskon)];
                            if ($diskonNominal > $totalJual) {
                                $diskonNominal = $totalJual * 0.1;
                            }
                            $tipeDiskon = 'nominal';
                        }
                    }

                    $totalPenjualanAkhir = $totalJual - $diskonNominal;

                    $trx->update([
                        'subtotal' => $totalJual,
                        'diskon' => $diskonNominal,
                        'tipe_diskon' => $tipeDiskon,
                        'total_penjualan' => $totalPenjualanAkhir,
                        'total_hpp' => $totalHpp
                    ]);
                }

                // 5% chance to have a return on one of the items
                if (rand(1, 100) <= 5 && count($trx->details) > 0) {
                    $detailToReturn = $trx->details->random();
                    if ($detailToReturn->qty > 1) {
                        $qtyRetur = rand(1, $detailToReturn->qty - 1);
                        $jumlahRefund = $qtyRetur * $detailToReturn->harga_satuan;
                        
                        $retur = ReturnItem::create([
                            'transaction_detail_id' => $detailToReturn->id,
                            'user_id' => $admin->id,
                            'qty_retur' => $qtyRetur,
                            'alasan' => collect(['Barang rusak', 'Salah beli', 'Cacat pabrik'])->random(),
                            'jumlah_refund' => $jumlahRefund,
                            'created_at' => $trx->created_at->copy()->addHours(rand(1, 48)),
                            'updated_at' => $trx->created_at->copy()->addHours(rand(1, 48)),
                        ]);

                        // Return stock
                        $detailToReturn->product->increment('stok', $qtyRetur);

                        // Audit Log for return
                        AuditLog::create([
                            'user_id' => $admin->id,
                            'event' => 'custom',
                            'auditable_type' => 'Custom',
                            'auditable_id' => 0,
                            'old_values' => null,
                            'new_values' => ['description' => "Retur Penjualan Nota: {$trx->nomor_nota}, Produk: {$detailToReturn->product->nama_barang}, Qty: {$qtyRetur}, Refund: Rp " . number_format($jumlahRefund, 0, ',', '.')],
                            'ip_address' => '127.0.0.1',
                            'created_at' => $retur->created_at,
                            'updated_at' => $retur->updated_at,
                        ]);
                    }
                }
            }

            $currentDate->addDay();
        }

        // ==========================================
        // 6. BUAT UTANG JATUH TEMPO & BELUM JATUH TEMPO
        // ==========================================
        // 1 Overdue (Telat)
        $pOverdue = $products[0];
        $totalHarga1 = 50 * $pOverdue->hpp;
        Purchase::create([
            'user_id' => $admin->id,
            'supplier_id' => $supMap[$pOverdue->id]->id,
            'nama_supplier' => $supMap[$pOverdue->id]->nama_supplier,
            'product_id' => $pOverdue->id,
            'nomor_faktur' => 'INV-SUP-OVERDUE-1',
            'qty_masuk' => 50,
            'harga_beli' => $pOverdue->hpp,
            'total_beli' => $totalHarga1,
            'tanggal_masuk' => Carbon::now()->subDays(35),
            'tanggal_jatuh_tempo' => Carbon::now()->subDays(5), // Telat 5 hari
            'status_bayar' => 'belum_lunas',
            'sisa_hutang' => $totalHarga1,
        ]);
        $pOverdue->increment('stok', 50);

        // 3 Normal Debts (Belum Jatuh Tempo, Panjang)
        for ($i=1; $i<=3; $i++) {
            $pNormal = $products[$i];
            $totalHargaNormal = 30 * $pNormal->hpp;
            Purchase::create([
                'user_id' => $admin->id,
                'supplier_id' => $supMap[$pNormal->id]->id,
                'nama_supplier' => $supMap[$pNormal->id]->nama_supplier,
                'product_id' => $pNormal->id,
                'nomor_faktur' => 'INV-SUP-ACTIVE-' . $i,
                'qty_masuk' => 30,
                'harga_beli' => $pNormal->hpp,
                'total_beli' => $totalHargaNormal,
                'tanggal_masuk' => Carbon::now()->subDays(5),
                'tanggal_jatuh_tempo' => Carbon::now()->addDays(25), // Masih lama
                'status_bayar' => 'belum_lunas',
                'sisa_hutang' => $totalHargaNormal,
            ]);
            $pNormal->increment('stok', 30);
        }

        // ==========================================
        // 7. SET STOK SEBAGIAN BESAR TIPIS
        // ==========================================
        foreach ($products as $index => $prod) {
            // 80% products will be 'tipis' (1 to stok_minimum)
            if (rand(1, 100) <= 80) {
                $prod->update(['stok' => rand(1, $prod->stok_minimum)]);
            } else {
                // 20% are normal or empty
                $prod->update(['stok' => rand($prod->stok_minimum + 1, $prod->stok_minimum + 50)]);
            }
        }

        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
