<?php

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository
{
    public function __construct(
        private Invoice $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->with('basket.items')->get();
    }

    public function findById(int $id): ?Invoice
    {
        return $this->model->with('basket.items')->find($id);
    }

    public function create(array $data): Invoice
    {
        return $this->model->create($data);
    }
}
