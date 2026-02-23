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
        private InvoiceRepository $invoiceRepository,
        private ProductResolver $productResolver
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

    public function addItem(Basket $basket, array $data): array
    {
        $this->validateBasketModifiable($basket);

        $product = $this->productResolver->find($data['product_type'], $data['product_id']);

        if (! $product) {
            throw new \DomainException('Product not found');
        }

        $quantity = (int) ($data['quantity'] ?? 1);
        $quantityStock = (int) $product->quantity_stock;

        if ($quantityStock < $quantity) {
            throw new \DomainException("Insufficient stock. Available: {$quantityStock}, requested: {$quantity}");
        }

        $price = $this->productResolver->getSalePrice($product);

        $item = $this->basketItemRepository->create([
            'basket_id' => $basket->id,
            'product_type' => $data['product_type'],
            'product_id' => $product->id,
            'price' => $price,
            'quantity' => $quantity,
        ]);

        $product->decrement('quantity_stock', $quantity);

        return [
            'item' => $item,
            'product' => $product,
        ];
    }

    public function removeItem(Basket $basket, BasketItem $item): void
    {
        $this->validateBasketModifiable($basket);

        if ($item->basket_id !== $basket->id) {
            throw new \DomainException('Item does not belong to this basket');
        }

        $product = $this->productResolver->find($item->product_type, $item->product_id);

        if ($product) {
            $product->increment('quantity_stock', (int) $item->quantity);
        }

        $this->basketItemRepository->delete($item);
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
