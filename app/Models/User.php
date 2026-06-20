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

    public function carts()
    {
        return $this->hasMany(Cart::class, 'user_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'id');
    }
}
