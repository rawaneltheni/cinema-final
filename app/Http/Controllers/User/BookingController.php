<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\SeatReservation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    // Show the booking page for one specific showtime (movie).
    public function create(Request $request, Movie $movie): View
    {
        return view('User.booking', [
            'movie' => $movie,
            'bookedSeats' => $this->bookedSeats($this->reservationsFor($movie)),
            'accountName' => $this->accountName($request),
            'username' => $request->session()->get('username'),
        ]);
    }

    // Reserve a seat for this showtime.
    public function store(Request $request, Movie $movie): RedirectResponse
    {
        if ($movie->movie_status !== 'Showing' || $movie->available_seats < 1) {
            return back()->withErrors(['seat_number' => 'This movie is not available for booking.']);
        }

        $this->normalizeSeatNumber($request);
        // The customer is always the logged-in account holder.
        $request->merge(['customer_name' => $this->accountName($request)]);
        $data = $request->validate($this->rules($movie));

        DB::transaction(function () use ($movie, $data, $request) {
            SeatReservation::create([
                'user_id' => $this->currentUserId($request),
                'show_id' => $movie->show_id,
                'customer_name' => $data['customer_name'],
                'seat_number' => $data['seat_number'],
            ]);

            // Booking a seat reduces the movie's remaining seats.
            $movie->decrement('available_seats');
        });

        return redirect()
            ->route('movies.booking', $movie)
            ->with('status', 'Seat '.$data['seat_number'].' reserved for '.$data['customer_name'].'.');
    }

    public function edit(Request $request, SeatReservation $seatReservation): View
    {
        $movie = $seatReservation->movie;

        return view('reservations.edit', [
            'reservation' => $seatReservation,
            'movie' => $movie,
            // Exclude this reservation's own seat so it stays selectable.
            'bookedSeats' => $this->bookedSeats(
                $this->reservationsFor($movie)->where('id', '!=', $seatReservation->id)
            ),
            'accountName' => $seatReservation->customer_name,
            'username' => $request->session()->get('username'),
        ]);
    }

    public function update(Request $request, SeatReservation $seatReservation): RedirectResponse
    {
        $movie = $seatReservation->movie;

        $this->normalizeSeatNumber($request);
        // Editing only changes the seat; the customer stays the account holder.
        $request->merge(['customer_name' => $seatReservation->customer_name]);
        $data = $request->validate($this->rules($movie, $seatReservation));

        // Only the customer and seat can change; the showtime stays the same,
        // so available_seats is unaffected.
        $seatReservation->update([
            'customer_name' => $data['customer_name'],
            'seat_number' => $data['seat_number'],
        ]);

        return redirect()->route('movies.booking', $movie)->with('status', 'Reservation updated.');
    }

    public function destroy(SeatReservation $seatReservation): RedirectResponse
    {
        $movie = $seatReservation->movie;

        DB::transaction(function () use ($seatReservation, $movie) {
            $seatReservation->delete();
            // Cancelling a reservation frees the seat again.
            $movie?->increment('available_seats');
        });

        return redirect()->route('movies.booking', $movie)->with('status', 'Reservation cancelled.');
    }

    private function reservationsFor(Movie $movie): Collection
    {
        return SeatReservation::query()
            ->where('show_id', $movie->show_id)
            ->orderBy('seat_number')
            ->get();
    }

    private function bookedSeats(Collection $reservations): Collection
    {
        return $reservations->pluck('seat_number')->map(fn ($seat) => strtoupper($seat))->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(Movie $movie, ?SeatReservation $reservation = null): array
    {
        $uniqueSeat = Rule::unique('seat_reservations', 'seat_number')
            ->where('show_id', $movie->show_id);

        if ($reservation) {
            $uniqueSeat->ignore($reservation->id);
        }

        return [
            'customer_name' => ['required', 'string', 'max:80'],
            'seat_number' => ['required', 'string', 'max:2', 'regex:/^[ABC][1-6]$/', $uniqueSeat],
        ];
    }

    private function currentUserId(Request $request): ?int
    {
        return User::where('username', $request->session()->get('username'))->value('id');
    }

    // The display name to book under: the account's name, or its username.
    private function accountName(Request $request): string
    {
        $username = (string) $request->session()->get('username');
        $user = User::where('username', $username)->first();

        return $user?->name ?: $username;
    }

    private function normalizeSeatNumber(Request $request): void
    {
        $request->merge([
            'seat_number' => strtoupper(trim((string) $request->input('seat_number'))),
        ]);
    }
}
