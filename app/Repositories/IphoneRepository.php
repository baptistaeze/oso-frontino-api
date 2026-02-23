<?php

namespace App\Repositories;

use App\Models\Iphone;
use Illuminate\Database\Eloquent\Collection;

class IphoneRepository
{
    public function __construct(
        private Iphone $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Iphone
    {
        return $this->model->find($id);
    }

    public function create(array $data): Iphone
    {
        return $this->model->create($data);
    }

    public function update(Iphone $iphone, array $data): Iphone
    {
        $iphone->update($data);

        return $iphone->fresh();
    }

    public function delete(Iphone $iphone): bool
    {
        return $iphone->delete();
    }
}
