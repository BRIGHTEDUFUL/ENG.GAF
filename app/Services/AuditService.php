<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AuditService
{
    public function log(
        string $event,
        ?User $user = null,
        ?Model $auditable = null,
        array $oldValues = [],
        array $newValues = [],
        ?Request $request = null
    ): void {
        try {
            AuditLog::create([
                'user_id'        => $user?->id,
                'event'          => $event,
                'auditable_type' => $auditable ? get_class($auditable) : null,
                'auditable_id'   => $auditable?->getKey(),
                'old_values'     => $oldValues ?: null,
                'new_values'     => $newValues ?: null,
                'ip_address'     => $request?->ip(),
                'user_agent'     => $request ? substr($request->userAgent() ?? '', 0, 500) : null,
                'created_at'     => now(),
            ]);
        } catch (\Throwable) {
            // Never let audit logging break the application
        }
    }

    public function logAuth(string $event, User $user, Request $request): void
    {
        $this->log($event, $user, null, [], [], $request);
    }

    public function logPolicyDenied(User $user, string $action, string $model): void
    {
        $this->log('policy_denied', $user, null, [], [
            'action' => $action,
            'model'  => $model,
        ]);
    }

    public function logModelCreated(User $user, Model $model): void
    {
        $this->log('created', $user, $model, [], $model->toArray());
    }

    public function logModelUpdated(User $user, Model $model, array $oldValues): void
    {
        $this->log('updated', $user, $model, $oldValues, $model->toArray());
    }

    public function logModelDeleted(User $user, Model $model): void
    {
        $this->log('deleted', $user, $model, $model->toArray(), []);
    }
}
