<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained(); // Siapa yang melakukan
            $table->string('event'); // created, updated, deleted, restored
            $table->string('auditable_type'); // Model apa yang diubah (misal: App\Models\Transaction)
            $table->unsignedBigInteger('auditable_id'); // ID dari record yang diubah
            $table->json('old_values')->nullable(); // Data SEBELUM diubah
            $table->json('new_values')->nullable(); // Data SESUDAH diubah
            $table->string('ip_address')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
