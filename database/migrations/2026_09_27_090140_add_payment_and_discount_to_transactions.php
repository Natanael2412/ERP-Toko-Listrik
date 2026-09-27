<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('metode_pembayaran', 50)->default('Cash')->after('total_penjualan');
            $table->string('tipe_diskon', 20)->default('nominal')->after('subtotal'); // nominal / persen
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['metode_pembayaran', 'tipe_diskon']);
        });
    }
};
