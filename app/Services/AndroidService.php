<?php

namespace App\Services;

use App\Models\Android;
use App\Repositories\AndroidRepository;
use Illuminate\Database\Eloquent\Collection;

class AndroidService
{
    public function __construct(
        private AndroidRepository $repository
    ) {}

    public function listar(): Collection
    {
        return $this->repository->getAll();
    }

    public function crear(array $data): Android
    {
        return $this->repository->create($data);
    }

    public function obtener(int $id): ?Android
    {
        return $this->repository->findById($id);
    }

    public function actualizar(Android $android, array $data): Android
    {
        return $this->repository->update($android, $data);
    }

    public function eliminar(Android $android): bool
    {
        return $this->repository->delete($android);
    }
}
