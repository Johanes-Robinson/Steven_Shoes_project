<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'category',
        'size',
        'description',
        'image_url',
        'price',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->id)) {
                $product->id = (string) Str::uuid();
            }
        });

        static::created(function (Product $product) {
            $now = now();

            $product->sizes()->createMany(
                collect(static::availableSizeRange())
                    ->map(fn (int $size) => [
                        'size' => $size,
                        'is_available' => (int) $product->size === $size && $product->is_available,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])
                    ->all()
            );
        });
    }

    public static function availableSizeRange(): array
    {
        return range(36, 46);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'product_id', 'id');
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'product_id', 'id');
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class, 'product_id', 'id');
    }

    public function isSizeAvailable(int $size): bool
    {
        if ($this->relationLoaded('sizes')) {
            return (bool) ($this->sizes->firstWhere('size', $size)?->is_available ?? false);
        }

        return (bool) $this->sizes()
            ->where('size', $size)
            ->value('is_available');
    }

    public function hasAvailableSize(int $size): bool
    {
        return $this->is_available
            && in_array($size, static::availableSizeRange(), true)
            && $this->isSizeAvailable($size);
    }

    public function sizeAvailability(): array
    {
        $availability = array_fill_keys(static::availableSizeRange(), false);
        $sizes = $this->relationLoaded('sizes') ? $this->sizes : $this->sizes()->get();

        foreach ($sizes as $size) {
            $availability[(int) $size->size] = (bool) $size->is_available;
        }

        return $availability;
    }

    public function availableSizes(): array
    {
        return array_keys(array_filter($this->sizeAvailability()));
    }

    public function getCategorySlugAttribute(): string
    {
        return Str::slug($this->category ?? '');
    }

    public function getCategoryNameAttribute(): string
    {
        return Str::of($this->category ?? 'Produk')->replace('-', ' ')->title()->toString();
    }

}
