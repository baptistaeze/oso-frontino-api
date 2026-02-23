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

    public function listar(): Collection
    {
        return $this->repository->getAll();
    }

    public function crear(array $data): Audifono
    {
        return $this->repository->create($data);
    }

    public function obtener(int $id): ?Audifono
    {
        return $this->repository->findById($id);
    }

    public function actualizar(Audifono $audifono, array $data): Audifono
    {
        return $this->repository->update($audifono, $data);
    }

    public function eliminar(Audifono $audifono): bool
    {
        return $this->repository->delete($audifono);
    }
}
