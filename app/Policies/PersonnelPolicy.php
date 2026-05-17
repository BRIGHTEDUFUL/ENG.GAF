<?php

namespace App\Policies;

use App\Models\User;

class PersonnelPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) return true;
        return null;
    }

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, User $personnel): bool { return true; }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'commander', 'supervisor']);
    }

    public function update(User $user, User $personnel): bool
    {
        return $user->hasRole(['admin', 'commander', 'supervisor']);
    }

    public function delete(User $user, User $personnel): bool
    {
        return $user->isAdmin();
    }

    public function export(User $user): bool
    {
        return true;
    }
}
