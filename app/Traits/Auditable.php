<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    /**
     * Mencatat aktivitas user ke tabel audit_logs.
     *
     * @param string $aksi      Jenis aksi (misal: 'Tambah Produk', 'Hapus User')
     * @param string $detail    Detail perubahan yang terjadi
     */
    protected function catatAudit(string $aksi, string $detail): void
    {
        if (auth()->check()) {
            AuditLog::create([
                'user_id' => auth()->id(),
                'aksi' => $aksi,
                'detail_perubahan' => $detail,
                'timestamp' => now(),
            ]);
        }
    }
}
