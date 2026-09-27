<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->string('nomor_faktur', 50);
            $table->string('nama_supplier', 100);
            $table->integer('qty_masuk');
            $table->double('harga_beli', 15, 2);
            $table->double('total_beli', 15, 2);
            $table->timestamp('tanggal_masuk')->useCurrent();
            $table->date('tanggal_jatuh_tempo');
            $table->enum('status_bayar', ['belum_lunas', 'lunas'])->default('belum_lunas');
            $table->double('sisa_hutang', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
