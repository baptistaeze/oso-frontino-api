<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SmartWatchUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'quantity_stock' => 'nullable|integer|min:0',
            'amount' => 'nullable|numeric|min:0',
            'image_path' => 'nullable|string|max:500',
        ];
    }
}
