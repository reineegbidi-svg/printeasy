<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingRule extends Model
{
    protected $fillable = [
        'printer_id', 'service_type', 'format', 'color_mode',
        'price_per_unit', 'setup_fee', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_unit' => 'decimal:2',
            'setup_fee' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function printer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printer_id');
    }
}
