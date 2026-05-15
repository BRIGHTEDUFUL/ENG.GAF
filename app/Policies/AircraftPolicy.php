<?php

namespace App\Policies;

use App\Models\Aircraft;
use App\Models\User;

class AircraftPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Aircraft $aircraft): bool { return true; }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'commander']);
    }

    public function update(User $user, Aircraft $aircraft): bool
    {
        return $user->hasRole(['admin', 'commander']);
    }

    public function delete(User $user, Aircraft $aircraft): bool
    {
        return $user->isAdmin();
    }
}
