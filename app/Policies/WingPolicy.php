<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wing;

class WingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Wing $wing): bool
    {
        return true;
    }

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
}
