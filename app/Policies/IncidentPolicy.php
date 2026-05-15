<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) return true;
        return null;
    }

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Incident $incident): bool { return true; }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'commander', 'supervisor']);
    }

    public function update(User $user, Incident $incident): bool
    {
        return $user->hasRole(['admin', 'commander', 'supervisor']);
    }

    public function delete(User $user, Incident $incident): bool
    {
        return $user->hasRole(['admin', 'commander']);
    }

    public function export(User $user): bool
    {
        return $user->hasRole(['admin', 'commander', 'auditor', 'supervisor']);
    }
}
