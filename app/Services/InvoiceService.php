<?php

namespace App\Services;

use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use Illuminate\Database\Eloquent\Collection;

class InvoiceService
{
    public function __construct(
        private InvoiceRepository $repository
    ) {}

    public function list(): Collection
    {
        return $this->repository->getAll();
    }

    public function findById(int $id): ?Invoice
    {
        return $this->repository->findById($id);
    }
}
