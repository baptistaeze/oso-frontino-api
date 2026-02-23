<?php

namespace App\Repositories;

use App\Models\Basket;
use Illuminate\Database\Eloquent\Collection;

class BasketRepository
{
    public function __construct(
        private Basket $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->with('items')->get();
    }

    public function findById(int $id): ?Basket
    {
        return $this->model->with('items')->find($id);
    }

    public function create(array $data = []): Basket
    {
        return $this->model->create(array_merge(['status' => 'pending'], $data));
    }

    public function update(Basket $basket, array $data): Basket
    {
        $basket->update($data);

        return $basket->fresh(['items']);
    }

    public function delete(Basket $basket): bool
    {
        return $basket->delete();
    }
}
