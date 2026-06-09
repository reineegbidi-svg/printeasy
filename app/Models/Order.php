<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference', 'user_id', 'printer_id', 'service_type', 'format',
        'color_mode', 'quantity', 'pages', 'page_start', 'page_end', 'unit_price', 'total_price',
        'status', 'payment_method', 'payment_status', 'file_path', 'file_name',
        'file_type', 'file_size', 'notes', 'rejection_reason', 'completed_at', 'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'completed_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function printer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printer_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(OrderFile::class);
    }
}
