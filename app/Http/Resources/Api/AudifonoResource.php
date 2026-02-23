<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AudifonoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'discount' => (float) $this->discount,
            'quantityStock' => $this->quantity_stock,
            'amount' => $this->amount ? (float) $this->amount : null,
            'imagePath' => $this->image_path
                ? (str_starts_with($this->image_path, 'http') ? $this->image_path : url($this->image_path))
                : null,
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}
