<?php

namespace App\Repositories;

use App\Models\SmartWatch;
use Illuminate\Database\Eloquent\Collection;

class SmartWatchRepository
{
    public function __construct(
        private SmartWatch $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?SmartWatch
    {
        return $this->model->find($id);
    }

    public function create(array $data): SmartWatch
    {
        return $this->model->create($data);
    }

    public function update(SmartWatch $smartWatch, array $data): SmartWatch
    {
        $smartWatch->update($data);

        return $smartWatch->fresh();
    }

    public function delete(SmartWatch $smartWatch): bool
    {
        return $smartWatch->delete();
    }
}
