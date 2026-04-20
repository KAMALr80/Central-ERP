<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->recordAuditLog('created');
        });

        static::updated(function ($model) {
            $model->recordAuditLog('updated');
        });

        static::deleted(function ($model) {
            $model->recordAuditLog('deleted');
        });
    }

    public function recordAuditLog($event)
    {
        $oldValues = null;
        $newValues = $this->getAttributes();

        if ($event === 'updated') {
            $oldValues = array_intersect_key($this->getOriginal(), $this->getDirty());
            $newValues = $this->getDirty();
        } elseif ($event === 'deleted') {
            $oldValues = $this->getAttributes();
            $newValues = null;
        }

        // Remove sensitive fields
        $sensitiveFields = ['password', 'remember_token', 'otp'];
        if ($oldValues) {
            $oldValues = array_diff_key($oldValues, array_flip($sensitiveFields));
        }
        if ($newValues) {
            $newValues = array_diff_key($newValues, array_flip($sensitiveFields));
        }

        AuditLog::create([
            'user_id'        => Auth::id(),
            'event'          => $event,
            'auditable_type' => get_class($this),
            'auditable_id'   => $this->id,
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'url'            => Request::fullUrl(),
            'ip_address'     => Request::ip(),
            'user_agent'     => Request::userAgent(),
        ]);
    }
}
