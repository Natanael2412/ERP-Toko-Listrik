<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data awal untuk development.
     */
    public function run(): void
    {
        // Buat akun Owner default
        User::create([
            'username' => 'owner',
            'email'    => 'owner@tokolistrik.com',
            'password' => 'password', // Otomatis di-hash oleh model cast
            'role'     => 'owner',
        ]);

        // Buat akun Admin default
        User::create([
            'username' => 'admin',
            'email'    => 'admin@tokolistrik.com',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        // Buat akun Kasir default
        User::create([
            'username' => 'kasir',
            'email'    => 'kasir@tokolistrik.com',
            'password' => 'password',
            'role'     => 'kasir',
        ]);

        // ==========================================
        // DUMMY DATA UNTUK TESTING / SHOWCASE
        // ==========================================

        // 1. Kategori
        $kategoriKabel = \App\Models\Category::create(['nama_kategori' => 'Kabel & Kawat']);
        $kategoriLampu = \App\Models\Category::create(['nama_kategori' => 'Lampu & Penerangan']);
        $kategoriSaklar = \App\Models\Category::create(['nama_kategori' => 'Saklar & Stop Kontak']);
        $kategoriMCB = \App\Models\Category::create(['nama_kategori' => 'MCB & Pengaman']);

        // 2. Supplier
        $supplierPhilips = \App\Models\Supplier::create([
            'nama_supplier' => 'PT Philips Indonesia',
            'kontak' => '081234567890',
            'alamat' => 'Jakarta Selatan'
        ]);
        $supplierSupreme = \App\Models\Supplier::create([
            'nama_supplier' => 'PT Supreme Cable',
            'kontak' => '081987654321',
            'alamat' => 'Tangerang'
        ]);
        $supplierBroco = \App\Models\Supplier::create([
            'nama_supplier' => 'PT Broco Electrical',
            'kontak' => '081122334455',
            'alamat' => 'Bekasi'
        ]);

        // 3. Produk Dummy
        \App\Models\Product::create([
            'category_id' => $kategoriKabel->id,
            'sku' => \App\Models\Product::generateSKU($kategoriKabel->id),
            'nama_barang' => 'Kabel NYM 2x1.5 Supreme (Roll 50m)',
            'satuan' => 'Roll',
            'stok' => 15,
            'stok_minimum' => 5,
            'hpp' => 450000,
            'harga_jual' => 500000,
        ]);

        \App\Models\Product::create([
            'category_id' => $kategoriKabel->id,
            'sku' => \App\Models\Product::generateSKU($kategoriKabel->id),
            'nama_barang' => 'Kabel NYA 1x1.5 Hitam Eterna (Roll 100m)',
            'satuan' => 'Roll',
            'stok' => 20,
            'stok_minimum' => 10,
            'hpp' => 280000,
            'harga_jual' => 320000,
        ]);

        \App\Models\Product::create([
            'category_id' => $kategoriLampu->id,
            'sku' => \App\Models\Product::generateSKU($kategoriLampu->id),
            'nama_barang' => 'Lampu LED Philips 9 Watt Putih',
            'satuan' => 'Pcs',
            'stok' => 150,
            'stok_minimum' => 20,
            'hpp' => 35000,
            'harga_jual' => 45000,
        ]);

        \App\Models\Product::create([
            'category_id' => $kategoriLampu->id,
            'sku' => \App\Models\Product::generateSKU($kategoriLampu->id),
            'nama_barang' => 'Lampu LED Philips 12 Watt Putih',
            'satuan' => 'Pcs',
            'stok' => 100,
            'stok_minimum' => 20,
            'hpp' => 45000,
            'harga_jual' => 55000,
        ]);

        \App\Models\Product::create([
            'category_id' => $kategoriSaklar->id,
            'sku' => \App\Models\Product::generateSKU($kategoriSaklar->id),
            'nama_barang' => 'Saklar Engkel Broco Galleo',
            'satuan' => 'Pcs',
            'stok' => 50,
            'stok_minimum' => 10,
            'hpp' => 15000,
            'harga_jual' => 20000,
        ]);

        \App\Models\Product::create([
            'category_id' => $kategoriSaklar->id,
            'sku' => \App\Models\Product::generateSKU($kategoriSaklar->id),
            'nama_barang' => 'Stop Kontak Broco Galleo',
            'satuan' => 'Pcs',
            'stok' => 60,
            'stok_minimum' => 15,
            'hpp' => 18000,
            'harga_jual' => 23000,
        ]);

        \App\Models\Product::create([
            'category_id' => $kategoriMCB->id,
            'sku' => \App\Models\Product::generateSKU($kategoriMCB->id),
            'nama_barang' => 'MCB Schneider 1 Phase 10A',
            'satuan' => 'Pcs',
            'stok' => 30,
            'stok_minimum' => 5,
            'hpp' => 45000,
            'harga_jual' => 60000,
        ]);
    }
}
