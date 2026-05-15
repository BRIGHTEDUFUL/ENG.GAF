<?php

namespace App\Policies;

use App\Models\Personnel;
use App\Models\User;

class PersonnelPolicy
{
    /**
     * Determine whether the user can view any personnel records.
     * Any authenticated user may list personnel.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view a specific personnel record.
     * Any authenticated user may view a personnel record.
     */
    public function view(User $user, Personnel $personnel): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create personnel records.
     * Only admins may create personnel.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update a personnel record.
     * Only admins may update personnel.
     */
    public function update(User $user, Personnel $personnel): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete a personnel record.
     * Only admins may delete personnel.
     */
    public function delete(User $user, Personnel $personnel): bool
    {
        return $user->isAdmin();
    }
}
