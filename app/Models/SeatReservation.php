<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeatReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'show_id',
        'customer_name',
        'seat_number',
    ];

    // The showtime (movie) this seat was booked for.
    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class, 'show_id', 'show_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
