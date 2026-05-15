<?php

namespace App\Repositories;

use App\Models\MaintenanceTask;
use App\Repositories\Contracts\MaintenanceTaskRepositoryInterface;

class MaintenanceTaskRepository implements MaintenanceTaskRepositoryInterface
{
    public function all()
    {
        return MaintenanceTask::all();
    }

    public function find($id)
    {
        return MaintenanceTask::findOrFail($id);
    }

    public function create(array $data)
    {
        return MaintenanceTask::create($data);
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
