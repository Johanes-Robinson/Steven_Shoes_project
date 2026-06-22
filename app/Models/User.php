<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    public const ADMIN_EMAIL = 'pardameansteven@gmail.com';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'shoe_size',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'shoe_size' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->id)) {
                $user->id = (string) Str::uuid();
            }
        });

        static::saving(function (User $user) {
            $user->role = static::roleForEmail($user->email);
        });
    }

    public static function roleForEmail(?string $email): string
    {
        return static::isAdminEmail($email) ? 'admin' : 'customer';
    }

    public static function isAdminEmail(?string $email): bool
    {
        return strtolower(trim((string) $email)) === self::ADMIN_EMAIL;
    }

    public function isAdmin(): bool
    {
        return static::isAdminEmail($this->email);
    }

    public function syncRoleWithEmail(): void
    {
        $role = static::roleForEmail($this->email);

        if ($this->role !== $role) {
            $this->forceFill(['role' => $role])->save();
        }
    }

    public function whatsappNumber(): ?string
    {
        $number = preg_replace('/\D+/', '', (string) $this->phone);

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

    public function whatsappUrl(?string $message = null): ?string
    {
        $number = $this->whatsappNumber();

        if (! $number) {
            return null;
        }

        $query = filled($message) ? '?text='.rawurlencode($message) : '';

        return "https://wa.me/{$number}{$query}";
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'id');
    }
}
