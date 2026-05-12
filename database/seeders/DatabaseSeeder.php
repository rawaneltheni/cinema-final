<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Showtime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Cinema Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
            ],
        );

        $showtimes = [
            [
                'movie_title' => 'Midnight Chase',
                'genre' => 'Action',
                'hall_number' => 1,
                'show_date' => '2026-05-15',
                'start_time' => '18:00',
                'end_time' => '20:05',
                'available_seats' => 84,
                'ticket_price' => 12.50,
                'movie_status' => 'Showing',
            ],
            [
                'movie_title' => 'The Silent Room',
                'genre' => 'Horror',
                'hall_number' => 2,
                'show_date' => '2026-05-16',
                'start_time' => '21:00',
                'end_time' => '22:45',
                'available_seats' => 60,
                'ticket_price' => 10.00,
                'movie_status' => 'Showing',
            ],
            [
                'movie_title' => 'Future Lights',
                'genre' => 'Sci-Fi',
                'hall_number' => 3,
                'show_date' => '2026-06-01',
                'start_time' => '19:30',
                'end_time' => '21:40',
                'available_seats' => 120,
                'ticket_price' => 15.00,
                'movie_status' => 'Coming Soon',
            ],
        ];

        foreach ($showtimes as $showtime) {
            Showtime::firstOrCreate(
                [
                    'movie_title' => $showtime['movie_title'],
                    'show_date' => $showtime['show_date'],
                    'start_time' => $showtime['start_time'],
                ],
                $showtime,
            );
        }
    }
}
