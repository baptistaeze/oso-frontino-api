<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChargeBasketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'basket' => new BasketResource($this->resource['basket']),
            'invoice' => new InvoiceResource($this->resource['invoice']),
        ];
    }
}
