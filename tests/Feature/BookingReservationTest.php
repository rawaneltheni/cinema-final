<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\SeatReservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BookingReservationTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $username = 'user'): User
    {
        return User::create([
            'name' => ucfirst($username),
            'username' => $username,
            'email' => $username.'@example.com',
            'password' => Hash::make('password'),
        ]);
    }

    private function showing(int $seats = 10): Movie
    {
        return Movie::create([
            'movie_title' => 'Joker',
            'genre' => 'Drama',
            'hall_number' => 7,
            'show_date' => now()->addDay()->toDateString(),
            'start_time' => '18:00',
            'end_time' => '20:00',
            'available_seats' => $seats,
            'ticket_price' => 15.00,
            'movie_status' => 'Showing',
        ]);
    }

    public function test_user_logs_in_through_the_database_account(): void
    {
        $this->user();

        $this->post(route('login.store'), ['username' => 'user', 'password' => 'password'])
            ->assertRedirect(route('user.home'));
        $this->assertSame('user', session('username'));

        $this->post(route('login.store'), ['username' => 'user', 'password' => 'wrong'])
            ->assertSessionHasErrors('username');
    }

    public function test_booking_a_seat_ties_the_reservation_to_the_logged_in_user(): void
    {
        $user = $this->user();
        $movie = $this->showing(seats: 10);

        $this->withSession(['username' => 'user'])
            ->post(route('movies.booking.store', $movie), ['seat_number' => 'A1'])
            ->assertRedirect(route('movies.booking', $movie));

        $this->assertDatabaseHas('seat_reservations', [
            'user_id' => $user->id,
            'show_id' => $movie->show_id,
            'seat_number' => 'A1',
        ]);

        // Booking decrements the showtime's remaining seats.
        $this->assertSame(9, $movie->fresh()->available_seats);
    }

    public function test_booking_page_lists_the_users_own_reservations_with_actions(): void
    {
        $user = $this->user();
        $movie = $this->showing();
        $reservation = SeatReservation::create([
            'user_id' => $user->id,
            'show_id' => $movie->show_id,
            'customer_name' => $user->name,
            'seat_number' => 'B2',
        ]);

        $this->withSession(['username' => 'user'])
            ->get(route('movies.booking', $movie))
            ->assertOk()
            ->assertSee('Your reservations')
            ->assertSee('Seat B2')
            ->assertSee(route('reservations.edit', $reservation))
            ->assertSee(route('reservations.destroy', $reservation));
    }

    public function test_a_user_cannot_edit_or_delete_another_users_reservation(): void
    {
        $this->user('user');
        $other = $this->user('other');
        $movie = $this->showing();
        $foreign = SeatReservation::create([
            'user_id' => $other->id,
            'show_id' => $movie->show_id,
            'customer_name' => $other->name,
            'seat_number' => 'C3',
        ]);

        $session = ['username' => 'user'];

        $this->withSession($session)->get(route('reservations.edit', $foreign))->assertForbidden();
        $this->withSession($session)->put(route('reservations.update', $foreign), ['seat_number' => 'C4'])->assertForbidden();
        $this->withSession($session)->delete(route('reservations.destroy', $foreign))->assertForbidden();

        $this->assertDatabaseHas('seat_reservations', ['id' => $foreign->id, 'seat_number' => 'C3']);
    }

    public function test_a_user_can_update_and_cancel_their_own_reservation(): void
    {
        $user = $this->user();
        $movie = $this->showing(seats: 9);
        $reservation = SeatReservation::create([
            'user_id' => $user->id,
            'show_id' => $movie->show_id,
            'customer_name' => $user->name,
            'seat_number' => 'A1',
        ]);

        $this->withSession(['username' => 'user'])
            ->put(route('reservations.update', $reservation), ['seat_number' => 'A2'])
            ->assertRedirect(route('movies.booking', $movie));
        $this->assertSame('A2', $reservation->fresh()->seat_number);

        $this->withSession(['username' => 'user'])
            ->delete(route('reservations.destroy', $reservation))
            ->assertRedirect(route('movies.booking', $movie));
        $this->assertDatabaseMissing('seat_reservations', ['id' => $reservation->id]);

        // Cancelling frees the seat again.
        $this->assertSame(10, $movie->fresh()->available_seats);
    }
}
