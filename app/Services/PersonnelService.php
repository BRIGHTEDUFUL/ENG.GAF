<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PersonnelService
{
    public function list(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with('wing');

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                  ->orWhere('email', 'like', '%'.$filters['search'].'%');
            });
        }

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): User
    {
        return User::findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $personnel, array $data): User
    {
        if (empty($data['password'])) unset($data['password']);
        $personnel->update($data);
        return $personnel;
    }

    public function delete(User $personnel): bool
    {
        $personnel->delete();
        return true;
    }
}
