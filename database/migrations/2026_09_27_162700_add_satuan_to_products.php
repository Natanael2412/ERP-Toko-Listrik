<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom satuan ke products (jika belum ada)
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'satuan')) {
                $table->string('satuan', 20)->default('pcs')->after('nama_barang');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('satuan');
        });
    }
};
