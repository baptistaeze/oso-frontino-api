<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddBasketItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $item = $this->resource['item'];
        $product = $this->resource['product'];

        $productResource = $this->getProductResource($product);

        return [
            'id' => $item->id,
            'basketId' => $item->basket_id,
            'product' => $productResource,
            'quantity' => (int) $item->quantity,
            'price' => (float) $item->price,
            'subtotal' => (float) ($item->price * $item->quantity),
            'createdAt' => $item->created_at?->toIso8601String(),
        ];
    }

    private function getProductResource($product): array
    {
        return match (class_basename($product)) {
            'Iphone' => (new IphoneResource($product))->toArray(request()),
            'SmartWatch' => (new SmartWatchResource($product))->toArray(request()),
            'Android' => (new AndroidResource($product))->toArray(request()),
            'Audifono' => (new AudifonoResource($product))->toArray(request()),
            default => ['id' => $product->id],
        };
    }
}
