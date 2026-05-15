<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wing;

class WingPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) return true;
        return null;
    }

    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Wing $wing): bool { return true; }

    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'commander']);
    }

    public function update(User $user, Wing $wing): bool
    {
        return $user->hasRole(['admin', 'commander']);
    }

    public function delete(User $user, Wing $wing): bool
    {
        return $user->isAdmin();
    }

    public function export(User $user): bool
    {
        return $user->hasRole(['admin', 'commander', 'auditor']);
    }
}
