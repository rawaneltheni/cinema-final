<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Showtime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_bookings_require_an_admin_session(): void
    {
        $this->get(route('bookings.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_view_booking_details(): void
    {
        $this->createBooking();

        $this->withSession(['username' => 'admin'])
            ->get(route('bookings.index'))
            ->assertOk()
            ->assertSee('Booking Management')
            ->assertSee('Sara Ahmed')
            ->assertSee('Inception')
            ->assertSee('VIP')
            ->assertSee('A12, A13')
            ->assertSee('2 chairs')
            ->assertSee('Popcorn combo')
            ->assertSee('Paid')
            ->assertSee('Accept')
            ->assertSee('Reject');
    }

    public function test_admin_can_accept_or_reject_a_booking(): void
    {
        $booking = $this->createBooking();

        $this->withSession(['username' => 'admin'])
            ->patch(route('bookings.status', $booking), ['status' => 'accepted'])
            ->assertRedirect(route('bookings.index'));

        $this->assertSame('accepted', $booking->fresh()->status);
    }

    private function createBooking(): Booking
    {
        $showtime = Showtime::create([
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

        return Booking::create([
            'showtime_id' => $showtime->show_id,
            'customer_name' => 'Sara Ahmed',
            'customer_email' => 'sara@example.com',
            'chair_type' => 'VIP',
            'chair_count' => 2,
            'seat_numbers' => 'A12, A13',
            'snacks' => 'Popcorn combo',
            'status' => 'pending',
            'payment_status' => 'paid',
            'payment_amount' => 52,
            'payment_method' => 'Visa',
        ]);
    }
}
