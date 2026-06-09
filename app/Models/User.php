<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'avatar',
        'address', 'is_active', 'is_available', 'is_approved',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_available' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPrinter(): bool
    {
        return $this->role === 'printer';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isApprovedPrinter(): bool
    {
        return $this->isPrinter() && $this->is_active && $this->is_approved;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function printerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'printer_id');
    }

    public function pricingRules(): HasMany
    {
        return $this->hasMany(PricingRule::class, 'printer_id');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(PrinterAvailability::class, 'printer_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }
}
