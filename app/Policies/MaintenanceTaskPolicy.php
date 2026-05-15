<?php

namespace App\Policies;

use App\Models\MaintenanceTask;
use App\Models\User;

class MaintenanceTaskPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, MaintenanceTask $task): bool { return true; }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'supervisor', 'commander']);
    }

    public function update(User $user, MaintenanceTask $task): bool
    {
        if ($user->hasRole(['admin', 'supervisor', 'commander'])) return true;
        // Engineer can update their own assigned task
        return $user->isEngineer() && $task->assigned_to === $user->id;
    }

    public function delete(User $user, MaintenanceTask $task): bool
    {
        return $user->hasRole(['admin', 'supervisor']);
    }
}
