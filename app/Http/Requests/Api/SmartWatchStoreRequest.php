<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SmartWatchStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0|max:100',
            'cantidad_stock' => 'nullable|integer|min:0',
            'monto' => 'nullable|numeric|min:0',
            'imagen_path' => 'nullable|string|max:500',
        ];
    }
}
