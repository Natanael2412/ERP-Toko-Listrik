<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->string('nomor_nota', 50)->unique();
            $table->timestamp('tanggal_waktu')->useCurrent();
            $table->double('total_hpp', 15, 2)->default(0);
            $table->double('subtotal', 15, 2)->default(0);
            $table->double('diskon', 15, 2)->default(0);
            $table->double('total_penjualan', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
