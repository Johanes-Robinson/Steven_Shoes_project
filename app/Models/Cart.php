<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Cart extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'selected_size',
        'quantity',
    ];

    protected $appends = [
        'name',
        'image',
        'price',
    ];

    protected $casts = [
        'selected_size' => 'integer',
        'quantity' => 'integer',
    ];

    public function getSelectedSizeAttribute($value): ?int
    {
        return $value !== null
            ? (int) $value
            : ($this->product?->size ? (int) $this->product->size : null);
    }

    protected static function booted(): void
    {
        static::creating(function (Cart $cart) {
            if (empty($cart->id)) {
                $cart->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function getNameAttribute(): string
    {
        return (string) ($this->product?->name ?? '');
    }

    public function getImageAttribute(): string
    {
        return (string) ($this->product?->image_url ?? '');
    }

    public function getPriceAttribute(): float
    {
        return (float) ($this->product?->price ?? 0);
    }
}
