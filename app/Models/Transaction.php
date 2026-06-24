<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory;

    public const FINAL_STATUSES = [
        'selesai',
        'batal',
    ];

    public const PAID_STATUSES = [
        'diproses',
        'dikirim',
        'selesai',
    ];

    protected $table = 'transaction';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'status',
        'total_amount',
        'payment_method',
        'shipping_address',
        'shipping_courier',
        'customer_name',
        'customer_email',
        'customer_phone',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Transaction $transaction) {
            if (empty($transaction->id)) {
                $transaction->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id', 'id');
    }

    public function items()
    {
        return $this->details();
    }

    public function getInvoiceNumberAttribute(): string
    {
        return strtoupper(substr($this->id, 0, 8));
    }

    public function getTotalPriceAttribute(): float
    {
        return (float) ($this->total_amount ?? 0);
    }

    public function setTotalPriceAttribute($value): void
    {
        $this->attributes['total_amount'] = $value;
    }

    public static function isFinalStatus(?string $status): bool
    {
        return in_array($status, self::FINAL_STATUSES, true);
    }

    public function isFinal(): bool
    {
        return static::isFinalStatus($this->status);
    }

    public static function isPaidStatus(?string $status): bool
    {
        return in_array($status, self::PAID_STATUSES, true);
    }

    public function isPaid(): bool
    {
        return static::isPaidStatus($this->status);
    }

    public function canBeCanceled(): bool
    {
        return $this->status === 'menunggu_pembayaran';
    }

    public function customerName(): string
    {
        return (string) ($this->customer_name ?: $this->user?->name ?: 'Customer');
    }

    public function customerPhone(): ?string
    {
        return $this->customer_phone ?: $this->user?->phone;
    }

    public function customerWhatsappUrl(?string $message = null): ?string
    {
        $number = $this->whatsappNumber($this->customerPhone());

        if (! $number) {
            return null;
        }

        $query = filled($message) ? '?text='.rawurlencode($message) : '';

        return "https://wa.me/{$number}{$query}";
    }

    private function whatsappNumber(?string $phone): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $phone);

        if ($number === '') {
            return null;
        }

        if (str_starts_with($number, '620')) {
            return '62'.substr($number, 3);
        }

        if (str_starts_with($number, '0')) {
            return '62'.substr($number, 1);
        }

        if (str_starts_with($number, '8')) {
            return '62'.$number;
        }

        return $number;
    }
}
