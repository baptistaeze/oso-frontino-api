<?php

namespace App\Repositories;

use App\Models\BasketItem;

class BasketItemRepository
{
    public function __construct(
        private BasketItem $model
    ) {}

    public function create(array $data): BasketItem
    {
        return $this->model->create($data);
    }

    public function delete(BasketItem $item): bool
    {
        return $item->delete();
    }
}
