<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('sku', 50)->unique();
            $table->string('nama_barang', 100);
            $table->integer('stok')->default(0);
            $table->integer('stok_minimum')->default(0);
            $table->double('hpp', 15, 2)->default(0);
            $table->double('harga_jual', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
