<?php

namespace App\Services;

use App\Models\Android;
use App\Models\Audifono;
use App\Models\Iphone;
use App\Models\SmartWatch;
use Illuminate\Database\Eloquent\Model;

class ProductResolver
{
    private const TYPE_MODEL_MAP = [
        'iphone' => Iphone::class,
        'smart_watch' => SmartWatch::class,
        'android' => Android::class,
        'audifono' => Audifono::class,
    ];

    /**
     * Find product by type and id. Returns null if not found.
     */
    public function find(string $productType, int $productId): ?Model
    {
        $modelClass = self::TYPE_MODEL_MAP[$productType] ?? null;

        if (! $modelClass) {
            return null;
        }

        return $modelClass::find($productId);
    }

    /**
     * Get the sale price (amount if set, otherwise price).
     */
    public function getSalePrice(Model $product): float
    {
        $amount = $product->amount ?? null;

        return (float) ($amount ?: $product->price);
    }
}
