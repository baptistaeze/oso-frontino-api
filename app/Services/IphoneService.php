<?php

namespace App\Services;

use App\Models\Iphone;
use App\Repositories\IphoneRepository;
use Illuminate\Database\Eloquent\Collection;

class IphoneService
{
    public function __construct(
        private IphoneRepository $repository
    ) {}

    public function list(): Collection
    {
        return $this->repository->getAll();
    }

    public function create(array $data): Iphone
    {
        return $this->repository->create($data);
    }

    public function findById(int $id): ?Iphone
    {
        return $this->repository->findById($id);
    }

    public function update(Iphone $iphone, array $data): Iphone
    {
        return $this->repository->update($iphone, $data);
    }

    public function delete(Iphone $iphone): bool
    {
        return $this->repository->delete($iphone);
    }
}
