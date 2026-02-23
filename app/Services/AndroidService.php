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

    public function list(): Collection
    {
        return $this->repository->getAll();
    }

    public function create(array $data): Android
    {
        return $this->repository->create($data);
    }

    public function findById(int $id): ?Android
    {
        return $this->repository->findById($id);
    }

    public function update(Android $android, array $data): Android
    {
        return $this->repository->update($android, $data);
    }

    public function delete(Android $android): bool
    {
        return $this->repository->delete($android);
    }
}
