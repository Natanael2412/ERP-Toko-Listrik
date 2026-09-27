<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            // Restrict pada produk untuk menghindari phantom stock akibat barang dihapus dari master
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->integer('qty');
            $table->double('harga_satuan', 15, 2);
            $table->double('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
