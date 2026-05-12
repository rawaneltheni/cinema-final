<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'movie_title',
    'genre',
    'hall_number',
    'show_date',
    'start_time',
    'end_time',
    'available_seats',
    'ticket_price',
    'movie_status',
])]
class Showtime extends Model
{
    use HasFactory;

    protected $primaryKey = 'show_id';

    protected function casts(): array
    {
        return [
            'show_date' => 'date',
            'ticket_price' => 'decimal:2',
        ];
    }
}
