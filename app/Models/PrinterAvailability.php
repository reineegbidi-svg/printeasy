<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrinterAvailability extends Model
{
    protected $fillable = [
        'printer_id', 'day_of_week', 'start_time', 'end_time', 'is_available',
    ];

    protected function casts(): array
    {
        return ['is_available' => 'boolean'];
    }

    public function printer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'printer_id');
    }
}
