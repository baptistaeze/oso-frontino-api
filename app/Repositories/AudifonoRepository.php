<?php

namespace App\Repositories;

use App\Models\Audifono;
use Illuminate\Database\Eloquent\Collection;

class AudifonoRepository
{
    public function __construct(
        private Audifono $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Audifono
    {
        return $this->model->find($id);
    }

    public function create(array $data): Audifono
    {
        return $this->model->create($data);
    }

    public function update(Audifono $audifono, array $data): Audifono
    {
        $audifono->update($data);

        return $audifono->fresh();
    }

    public function delete(Audifono $audifono): bool
    {
        return $audifono->delete();
    }
}
