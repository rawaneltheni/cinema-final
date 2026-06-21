<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'showtime_id',
    'customer_name',
    'customer_email',
    'chair_type',
    'chair_count',
    'seat_numbers',
    'snacks',
    'status',
    'payment_status',
    'payment_amount',
    'payment_method',
])]
class Booking extends Model
{
    use HasFactory;

    public function showtime(): BelongsTo
    {
        return $this->belongsTo(Showtime::class, 'showtime_id', 'show_id');
    }

    protected function casts(): array
    {
        return [
            'payment_amount' => 'decimal:2',
        ];
    }
}
