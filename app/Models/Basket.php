<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Basket extends Model
{
    use HasFactory;

    protected $fillable = ['status'];

    public function items(): HasMany
    {
        return $this->hasMany(BasketItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->price * $item->quantity);
    }
}
