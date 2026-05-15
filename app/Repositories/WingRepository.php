<?php

namespace App\Repositories;

use App\Models\Wing;
use App\Repositories\Contracts\WingRepositoryInterface;

class WingRepository implements WingRepositoryInterface
{
    public function all()
    {
        return Wing::all();
    }

    public function find($id)
    {
        return Wing::findOrFail($id);
    }

    public function create(array $data)
    {
        return Wing::create($data);
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
