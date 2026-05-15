<?php

namespace App\Policies;

use App\Models\Aircraft;
use App\Models\User;

class AircraftPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) return true;
        return null;
    }

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Aircraft $aircraft): bool { return true; }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'commander', 'supervisor']);
    }

    public function update(User $user, Aircraft $aircraft): bool
    {
        return $user->hasRole(['admin', 'commander', 'supervisor']);
    }

    public function delete(User $user, Aircraft $aircraft): bool
    {
        return $user->hasRole(['admin', 'commander']);
    }

    public function export(User $user): bool
    {
        return $user->hasRole(['admin', 'commander', 'auditor', 'supervisor']);
    }
}
