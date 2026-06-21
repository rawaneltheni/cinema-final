<?php

namespace Database\Seeders;

use App\Models\SeatReservation;
use App\Models\Showtime;
use App\Models\User;
use Carbon\Carbon;
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
        // Create or update the admin user used for logging in during testing.
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Cinema Admin',
                'email' => 'admin@example.com',
                // Hash the password before saving it to the database.
                'password' => Hash::make('password'),
            ],
        );

        // Create or update the standard customer account used for booking.
        $customer = User::updateOrCreate(
            ['username' => 'user'],
            [
                'name' => 'Cinema User',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
            ],
        );

        // Backfill reservations made before the customer account existed so they
        // are tied to a real user_id (they were saved with a null user_id).
        SeatReservation::whereNull('user_id')
            ->where('customer_name', 'user')
            ->update([
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
            ]);

        // Remove the three placeholder records that were used by the old seeder.
        Showtime::whereIn('movie_title', [
            'Midnight Chase',
            'The Silent Room',
            'Future Lights',
        ])->delete();

        // Real movie records used to fill the cinema showtimes table.
        $movies = [
            [
                'movie_name' => 'Inception',
                'genre' => 'Action',
                'duration' => 148,
                'release_date' => '2010-07-16',
                'release_place' => 'Main Cinema Hall',
                'language' => 'English',
                'director' => 'Christopher Nolan',
                'age_rating' => 'PG-13',
                'ticket_price' => 18.00,
                'available_seats' => 80,
                'image' => 'https://upload.wikimedia.org/wikipedia/en/2/2e/Inception_%282010%29_theatrical_poster.jpg',
                'description' => 'A skilled thief enters dreams to steal secrets and is offered one final impossible mission.',
            ],
            [
                'movie_name' => 'Interstellar',
                'genre' => 'Science Fiction',
                'duration' => 169,
                'release_date' => '2014-11-07',
                'release_place' => 'Galaxy Screen',
                'language' => 'English',
                'director' => 'Christopher Nolan',
                'age_rating' => 'PG-13',
                'ticket_price' => 20.00,
                'available_seats' => 95,
                'image' => 'https://upload.wikimedia.org/wikipedia/en/b/bc/Interstellar_film_poster.jpg',
                'description' => 'A team of astronauts travels through a wormhole to search for a new home for humanity.',
            ],
            [
                'movie_name' => 'The Dark Knight',
                'genre' => 'Action',
                'duration' => 152,
                'release_date' => '2008-07-18',
                'release_place' => 'Royal Screen',
                'language' => 'English',
                'director' => 'Christopher Nolan',
                'age_rating' => 'PG-13',
                'ticket_price' => 19.50,
                'available_seats' => 70,
                'image' => 'https://upload.wikimedia.org/wikipedia/en/thumb/1/1c/The_Dark_Knight_%282008_film%29.jpg/250px-The_Dark_Knight_%282008_film%29.jpg',
                'description' => 'Batman faces the Joker, a criminal mastermind who throws Gotham into chaos.',
            ],
            [
                'movie_name' => 'Avatar',
                'genre' => 'Science Fiction',
                'duration' => 162,
                'release_date' => '2009-12-18',
                'release_place' => 'IMAX Hall',
                'language' => 'English',
                'director' => 'James Cameron',
                'age_rating' => 'PG-13',
                'ticket_price' => 22.00,
                'available_seats' => 110,
                'image' => 'https://upload.wikimedia.org/wikipedia/en/d/d6/Avatar_%282009_film%29_poster.jpg',
                'description' => 'A former marine explores Pandora and becomes part of a conflict between humans and the Na\'vi.',
            ],
            [
                'movie_name' => 'Titanic',
                'genre' => 'Romance',
                'duration' => 195,
                'release_date' => '1997-12-19',
                'release_place' => 'Classic Cinema Hall',
                'language' => 'English',
                'director' => 'James Cameron',
                'age_rating' => 'PG-13',
                'ticket_price' => 16.00,
                'available_seats' => 64,
                'image' => 'https://upload.wikimedia.org/wikipedia/en/thumb/1/18/Titanic_%281997_film%29_poster.png/250px-Titanic_%281997_film%29_poster.png',
                'description' => 'A romantic epic set aboard the RMS Titanic during its tragic maiden voyage.',
            ],
            [
                'movie_name' => 'The Matrix',
                'genre' => 'Science Fiction',
                'duration' => 136,
                'release_date' => '1999-03-31',
                'release_place' => 'Digital Screen',
                'language' => 'English',
                'director' => 'The Wachowskis',
                'age_rating' => 'R',
                'ticket_price' => 17.50,
                'available_seats' => 88,
                'image' => 'https://upload.wikimedia.org/wikipedia/en/thumb/d/db/The_Matrix.png/250px-The_Matrix.png',
                'description' => 'A hacker discovers that reality is a simulation and joins a rebellion against machines.',
            ],
            [
                'movie_name' => 'Joker',
                'genre' => 'Drama',
                'duration' => 122,
                'release_date' => '2019-10-04',
                'release_place' => 'Drama Hall',
                'language' => 'English',
                'director' => 'Todd Phillips',
                'age_rating' => 'R',
                'ticket_price' => 15.00,
                'available_seats' => 56,
                'image' => 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e1/Joker_%282019_film%29_poster.jpg/250px-Joker_%282019_film%29_poster.jpg',
                'description' => 'A troubled comedian in Gotham City begins a dark transformation into the Joker.',
            ],
            [
                'movie_name' => 'Frozen',
                'genre' => 'Animation',
                'duration' => 102,
                'release_date' => '2013-11-27',
                'release_place' => 'Family Screen',
                'language' => 'Italian',
                'director' => 'Chris Buck and Jennifer Lee',
                'age_rating' => 'PG',
                'ticket_price' => 13.50,
                'available_seats' => 105,
                'image' => 'https://upload.wikimedia.org/wikipedia/en/thumb/0/05/Frozen_%282013_film%29_poster.jpg/250px-Frozen_%282013_film%29_poster.jpg',
                'description' => 'A princess sets out to find her sister whose icy powers trapped their kingdom in winter.',
            ],
        ];

        // The current project stores its movie catalogue in showtimes, so movie data is
        // mapped to the matching columns and a schedule is derived from its duration.
        foreach ($movies as $index => $movie) {
            $startTime = sprintf('%02d:00', 12 + $index);
            $endTime = Carbon::createFromFormat('H:i', $startTime)
                ->addMinutes($movie['duration'])
                ->format('H:i');

            // The show date is the upcoming screening date (not the release date),
            // so each movie is scheduled on a distinct day over the coming week.
            $showDate = Carbon::today()->addDays($index + 1)->format('Y-m-d');

            Showtime::updateOrCreate(
                ['movie_title' => $movie['movie_name']],
                [
                    'image' => $movie['image'],
                    'genre' => $movie['genre'],
                    'hall_number' => $index + 1,
                    'show_date' => $showDate,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'available_seats' => $movie['available_seats'],
                    'ticket_price' => $movie['ticket_price'],
                    'movie_status' => 'Showing',
                ],
            );
        }
    }
}
