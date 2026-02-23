<?php

namespace App\Repositories;

use App\Models\Android;
use Illuminate\Database\Eloquent\Collection;

class AndroidRepository
{
    public function __construct(
        private Android $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Android
    {
        return $this->model->find($id);
    }

    public function create(array $data): Android
    {
        return $this->model->create($data);
    }

    public function update(Android $android, array $data): Android
    {
        $android->update($data);

        return $android->fresh();
    }

    public function delete(Android $android): bool
    {
        return $android->delete();
    }
}
