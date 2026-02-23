<?php

namespace App\Http\Requests\Api;

use App\Rules\SafeInput;
use Illuminate\Foundation\Http\FormRequest;

class AndroidStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function validatedData(): array
    {
        $data = $this->safe()->except('image');

        if ($this->hasFile('image')) {
            $data['image_path'] = app(\App\Services\ProductImageService::class)
                ->store($this->file('image'), 'androids');
        }

        return $data;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', new SafeInput],
            'description' => ['nullable', 'string', new SafeInput],
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'quantity_stock' => 'nullable|integer|min:0',
            'amount' => 'nullable|numeric|min:0',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif|max:5120',
            'image_path' => ['nullable', 'string', 'max:500', new SafeInput],
        ];
    }
}
