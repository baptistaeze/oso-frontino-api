<?php

namespace App\Services;

use App\Models\Audifono;
use App\Repositories\AudifonoRepository;
use Illuminate\Database\Eloquent\Collection;

class AudifonoService
{
    public function __construct(
        private AudifonoRepository $repository
    ) {}

    public function list(): Collection
    {
        return $this->repository->getAll();
    }

    public function create(array $data): Audifono
    {
        return $this->repository->create($data);
    }

    public function findById(int $id): ?Audifono
    {
        return $this->repository->findById($id);
    }

    public function update(Audifono $audifono, array $data): Audifono
    {
        return $this->repository->update($audifono, $data);
    }

    public function delete(Audifono $audifono): bool
    {
        return $this->repository->delete($audifono);
    }
}
