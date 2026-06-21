<?php

namespace Tests\Feature;

use App\Models\Showtime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShowtimeImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_form_has_file_and_omdb_poster_inputs(): void
    {
        $this->withSession(['username' => 'admin'])
            ->get(route('showtimes.create'))
            ->assertOk()
            ->assertSee('id="image_file" type="file" name="image_file"', false)
            ->assertSee('type="hidden" name="image" id="image"', false);
    }

    public function test_an_admin_can_create_a_showtime_with_a_movie_image(): void
    {
        $image = 'https://example.com/inception-poster.jpg';

        $response = $this->withSession(['username' => 'admin'])
            ->post(route('showtimes.store'), $this->showtimeData(['image' => $image]));

        $response->assertRedirect(route('showtimes.index'));
        $this->assertDatabaseHas('showtimes', [
            'movie_title' => 'Inception',
            'image' => $image,
        ]);
    }

    public function test_an_admin_can_update_a_movie_image(): void
    {
        $showtime = Showtime::create($this->showtimeData([
            'image' => 'https://example.com/old-poster.jpg',
        ]));

        $newImage = 'https://example.com/new-poster.jpg';
        $response = $this->withSession(['username' => 'admin'])
            ->put(route('showtimes.update', $showtime), $this->showtimeData([
                'image' => $newImage,
            ]));

        $response->assertRedirect(route('showtimes.index'));
        $this->assertDatabaseHas('showtimes', [
            'show_id' => $showtime->show_id,
            'image' => $newImage,
        ]);
    }

    public function test_an_admin_can_upload_a_movie_image_file(): void
    {
        Storage::fake('public');

        $response = $this->withSession(['username' => 'admin'])
            ->post(route('showtimes.store'), $this->showtimeData([
                'image_file' => UploadedFile::fake()->image('poster.jpg', 400, 600),
                'image_changed' => '1',
            ]));

        $response->assertRedirect(route('showtimes.index'));

        $showtime = Showtime::where('movie_title', 'Inception')->firstOrFail();
        $storedPath = str_replace(Storage::disk('public')->url(''), '', $showtime->image);
        Storage::disk('public')->assertExists($storedPath);
    }

    public function test_omdb_movie_details_include_the_poster_url(): void
    {
        config(['services.omdb.key' => 'test-key']);
        Http::fake([
            '*' => Http::response([
                'Response' => 'True',
                'imdbID' => 'tt1375666',
                'Title' => 'Inception',
                'Genre' => 'Action, Science Fiction',
                'Poster' => 'https://example.com/omdb-poster.jpg',
            ]),
        ]);

        $this->getJson('/api/movies/tt1375666')
            ->assertOk()
            ->assertJsonPath('data.poster', 'https://example.com/omdb-poster.jpg');
    }

    public function test_omdb_na_poster_is_returned_as_null(): void
    {
        config(['services.omdb.key' => 'test-key']);
        Http::fake([
            '*' => Http::response([
                'Response' => 'True',
                'imdbID' => 'tt0000001',
                'Title' => 'No Poster Movie',
                'Poster' => 'N/A',
            ]),
        ]);

        $this->getJson('/api/movies/tt0000001')
            ->assertOk()
            ->assertJsonPath('data.poster', null);
    }

    public function test_the_seeder_adds_the_eight_requested_movies_with_images(): void
    {
        $this->seed();

        $this->assertDatabaseCount('showtimes', 8);
        $this->assertDatabaseHas('showtimes', [
            'movie_title' => 'Inception',
            'image' => 'https://upload.wikimedia.org/wikipedia/en/2/2e/Inception_%282010%29_theatrical_poster.jpg',
        ]);
        $this->assertSame(
            '2026-05-30',
            Showtime::where('movie_title', 'Inception')->firstOrFail()->show_date->format('Y-m-d'),
        );
        $this->assertDatabaseHas('showtimes', [
            'movie_title' => 'Inception',
            'movie_status' => 'Showing',
        ]);
        $this->assertDatabaseHas('showtimes', [
            'movie_title' => 'Interstellar',
            'movie_status' => 'Showing',
        ]);
        $this->assertDatabaseHas('showtimes', [
            'movie_title' => 'The Dark Knight',
            'movie_status' => 'Showing',
        ]);
        $this->assertDatabaseHas('showtimes', [
            'movie_title' => 'Frozen',
            'image' => 'https://upload.wikimedia.org/wikipedia/en/thumb/0/05/Frozen_%282013_film%29_poster.jpg/250px-Frozen_%282013_film%29_poster.jpg',
        ]);
    }

    private function showtimeData(array $overrides = []): array
    {
        return [
            'movie_title' => 'Inception',
            'image' => null,
            'genre' => 'Action',
            'hall_number' => 1,
            'show_date' => '2026-07-16',
            'start_time' => '18:00',
            'end_time' => '20:28',
            'available_seats' => 80,
            'ticket_price' => 18.00,
            'movie_status' => 'Showing',
            ...$overrides,
        ];
    }
}
