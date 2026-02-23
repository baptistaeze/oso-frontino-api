<?php

namespace App\Services;

use App\Models\Basket;
use App\Models\BasketItem;
use App\Repositories\BasketItemRepository;
use App\Repositories\BasketRepository;
use App\Repositories\InvoiceRepository;
use Illuminate\Database\Eloquent\Collection;

class BasketService
{
    public function __construct(
        private BasketRepository $basketRepository,
        private BasketItemRepository $basketItemRepository,
        private InvoiceRepository $invoiceRepository
    ) {}

    public function list(): Collection
    {
        return $this->basketRepository->getAll();
    }

    public function create(): Basket
    {
        return $this->basketRepository->create();
    }

    public function findById(int $id): ?Basket
    {
        return $this->basketRepository->findById($id);
    }

    public function update(Basket $basket, array $data): Basket
    {
        $this->validateBasketModifiable($basket);

        return $this->basketRepository->update($basket, $data);
    }

    public function delete(Basket $basket): bool
    {
        $this->validateBasketModifiable($basket);

        return $this->basketRepository->delete($basket);
    }

    public function addItem(Basket $basket, array $data): BasketItem
    {
        $this->validateBasketModifiable($basket);

        $data['basket_id'] = $basket->id;

        return $this->basketItemRepository->create($data);
    }

    public function charge(Basket $basket): array
    {
        if ($basket->status === 'charged') {
            throw new \DomainException('Basket is already charged');
        }

        if ($basket->items->isEmpty()) {
            throw new \DomainException('Basket is empty');
        }

        $total = (float) $basket->total;
        $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . str_pad($basket->id, 5, '0', STR_PAD_LEFT);

        $basket = $this->basketRepository->update($basket, ['status' => 'charged']);

        $invoice = $this->invoiceRepository->create([
            'basket_id' => $basket->id,
            'invoice_number' => $invoiceNumber,
            'total' => $total,
        ]);

        return [
            'basket' => $basket,
            'invoice' => $invoice,
        ];
    }

    private function validateBasketModifiable(Basket $basket): void
    {
        if ($basket->status === 'charged') {
            throw new \DomainException('Cannot modify a charged basket');
        }
    }
}
