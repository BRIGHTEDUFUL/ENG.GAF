<?php

namespace App\Repositories;

use App\Models\MaintenanceLog;
use App\Repositories\Contracts\MaintenanceLogRepositoryInterface;

class MaintenanceLogRepository implements MaintenanceLogRepositoryInterface
{
    public function all()
    {
        return MaintenanceLog::all();
    }

    public function find($id)
    {
        return MaintenanceLog::findOrFail($id);
    }

    public function create(array $data)
    {
        return MaintenanceLog::create($data);
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
