<?php

namespace App\Policies;

use App\Models\MaintenanceLog;
use App\Models\User;

class MaintenanceLogPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, MaintenanceLog $log): bool { return true; }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'engineer']);
    }

    public function update(User $user, MaintenanceLog $log): bool
    {
        if ($user->isAdmin()) return true;
        // Engineers can only edit their own draft logs
        if ($user->isEngineer() && $log->engineer_id === $user->id) {
            return $log->status === 'draft';
        }
        return false;
    }

    public function approve(User $user, MaintenanceLog $log): bool
    {
        return $user->hasRole(['admin', 'supervisor']);
    }

    public function delete(User $user, MaintenanceLog $log): bool
    {
        return $user->isAdmin();
    }
}
