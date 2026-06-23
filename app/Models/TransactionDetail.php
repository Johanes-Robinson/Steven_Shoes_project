<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TransactionDetail extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'quantity',
        'price',
        'product_name',
        'product_category',
        'product_size',
        'product_image_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'product_size' => 'integer',
        'quantity' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (TransactionDetail $detail) {
            if (empty($detail->id)) {
                $detail->id = (string) Str::uuid();
            }
        });
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function productName(): string
    {
        return (string) ($this->product_name ?: $this->product?->name ?: 'Produk dihapus');
    }

    public function productImageUrl(): string
    {
        return (string) ($this->product_image_url ?: $this->product?->image_url ?: 'https://placehold.co/100x100/F3ECDF/3E2511?text=Sepatu');
    }

    public function productSize(): ?int
    {
        return $this->product_size
            ? (int) $this->product_size
            : ($this->product?->size ? (int) $this->product->size : null);
    }
}
