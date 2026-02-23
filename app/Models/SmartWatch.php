<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartWatch extends Model
{
    use HasFactory;

    protected $table = 'smart_watches';

    protected $fillable = [
        'name',
        'description',
        'price',
        'discount',
        'quantity_stock',
        'amount',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }
}
