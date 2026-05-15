<?php

namespace App\Repositories;

use App\Models\Incident;
use App\Repositories\Contracts\IncidentRepositoryInterface;

class IncidentRepository implements IncidentRepositoryInterface
{
    public function all()
    {
        return Incident::all();
    }

    public function find($id)
    {
        return Incident::findOrFail($id);
    }

    public function create(array $data)
    {
        return Incident::create($data);
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
