<?php

namespace App\Services;

use App\Models\SmartWatch;
use App\Repositories\SmartWatchRepository;
use Illuminate\Database\Eloquent\Collection;

class SmartWatchService
{
    public function __construct(
        private SmartWatchRepository $repository
    ) {}

    public function list(): Collection
    {
        return $this->repository->getAll();
    }

    public function create(array $data): SmartWatch
    {
        return $this->repository->create($data);
    }

    public function findById(int $id): ?SmartWatch
    {
        return $this->repository->findById($id);
    }

    public function update(SmartWatch $smartWatch, array $data): SmartWatch
    {
        return $this->repository->update($smartWatch, $data);
    }

    public function delete(SmartWatch $smartWatch): bool
    {
        return $this->repository->delete($smartWatch);
    }
}
