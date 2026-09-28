<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        // Mencatat saat data DIBUAT
        static::created(function ($model) {
            self::logAudit($model, 'created');
        });

        // Mencatat saat data DIUBAH
        static::updated(function ($model) {
            self::logAudit($model, 'updated');
        });

        // Mencatat saat data DIHAPUS
        static::deleted(function ($model) {
            self::logAudit($model, 'deleted');
        });
    }

    protected static function logAudit($model, $event)
    {
        $oldValues = [];
        $newValues = [];

        if ($event === 'updated') {
            // Hanya ambil field yang benar-benar berubah
            $newValues = $model->getDirty(); 
            // Ambil value lama dari field yang berubah tersebut
            foreach ($newValues as $key => $value) {
                $oldValues[$key] = $model->getOriginal($key);
            }
        } elseif ($event === 'created') {
            $newValues = $model->getAttributes();
        } elseif ($event === 'deleted') {
            $oldValues = $model->getAttributes();
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => empty($oldValues) ? null : json_encode($oldValues),
            'new_values' => empty($newValues) ? null : json_encode($newValues),
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Backward compatibility untuk custom audit log dari controller.
     */
    public function catatAudit($event, $description)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'event' => substr($event, 0, 255),
            'auditable_type' => 'Custom',
            'auditable_id' => 0,
            'old_values' => null,
            'new_values' => json_encode(['description' => $description]),
            'ip_address' => request()->ip(),
        ]);
    }
}

