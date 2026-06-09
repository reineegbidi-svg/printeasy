<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderFile extends Model
{
    protected $fillable = [
        'order_id', 'file_path', 'file_name', 'file_type', 'file_size',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
