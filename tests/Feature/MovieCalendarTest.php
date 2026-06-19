<?php

namespace Tests\Feature;

use App\Models\Showtime;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MovieCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_requires_an_admin_session(): void
    {
        $this->get(route('calendar'))
            ->assertRedirect(route('login'));
    }

    public function test_calendar_displays_movies_for_the_selected_month(): void
    {
        $showtime = $this->createShowtime();

        $this->withSession(['username' => 'admin'])
            ->get(route('calendar', ['month' => '2026-07']))
            ->assertOk()
            ->assertSee('Movie Calendar')
            ->assertSee('July 2026')
            ->assertSee('Inception')
            ->assertSee('6:00 PM')
            ->assertSee('Hall 1')
            ->assertSee(route('showtimes.edit', $showtime), false);
    }

    public function test_admin_index_contains_showtime_and_calendar_sidebar_links(): void
    {
        $this->withSession(['username' => 'admin'])
            ->get(route('showtimes.index'))
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Movie Calendar')
            ->assertSee('id="sidebar_toggle"', false)
            ->assertSee('Hide sidebar')
            ->assertSee('Cinema Manager')
            ->assertSee(route('calendar'), false);
    }

    public function test_a_future_movie_is_automatically_saved_as_coming_soon(): void
    {
        CarbonImmutable::setTestNow('2026-06-19 12:00:00');

        try {
            $showtime = $this->createShowtime();

            $this->assertSame('Coming Soon', $showtime->movie_status);
        } finally {
            CarbonImmutable::setTestNow();
        }
    }

    public function test_seeded_movies_open_in_the_june_2026_calendar(): void
    {
        $this->seed();

        $this->withSession(['username' => 'admin'])
            ->get(route('calendar'))
            ->assertOk()
            ->assertSee('June 2026')
            ->assertSee('Interstellar')
            ->assertSee('Frozen');
    }

    private function createShowtime(): Showtime
    {
        return Showtime::create([
            'movie_title' => 'Inception',
            'image' => 'https://example.com/inception.jpg',
            'genre' => 'Action',
            'hall_number' => 1,
            'show_date' => '2026-07-16',
            'start_time' => '18:00',
            'end_time' => '20:28',
            'available_seats' => 80,
            'ticket_price' => 18,
            'movie_status' => 'Showing',
        ]);
    }
}
