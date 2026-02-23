<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class BasketItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_type' => 'required|in:iphone,smart_watch,android,audifono',
            'product_id' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->quantity === null) {
            $this->merge(['quantity' => 1]);
        }
    }
}
