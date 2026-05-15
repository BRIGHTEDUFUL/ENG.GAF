<?php

namespace App\Repositories;

use App\Models\Aircraft;
use App\Repositories\Contracts\AircraftRepositoryInterface;

class AircraftRepository implements AircraftRepositoryInterface
{
    public function all()
    {
        return Aircraft::all();
    }

    public function find($id)
    {
        return Aircraft::findOrFail($id);
    }

    public function create(array $data)
    {
        return Aircraft::create($data);
    }

    public function update($id, array $data)
    {
        $record = $this->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        $record = $this->find($id);
        return $record->delete();
    }
}
