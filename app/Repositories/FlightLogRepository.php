<?php

namespace App\Repositories;

use App\Models\FlightLog;
use App\Repositories\Contracts\FlightLogRepositoryInterface;

class FlightLogRepository implements FlightLogRepositoryInterface
{
    public function all()
    {
        return FlightLog::all();
    }

    public function find($id)
    {
        return FlightLog::findOrFail($id);
    }

    public function create(array $data)
    {
        return FlightLog::create($data);
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
