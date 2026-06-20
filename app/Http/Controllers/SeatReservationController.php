<?php

namespace App\Http\Controllers;

use App\Models\SeatReservation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SeatReservationController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $search = $validated['search'] ?? null;

        $reservations = SeatReservation::query()
            ->when($search, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('movie_title', 'like', "%{$search}%")
                        ->orWhere('theater', 'like', "%{$search}%")
                        ->orWhere('seat_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('show_date')
            ->orderBy('show_time')
            ->paginate(10)
            ->withQueryString();
            // This keeps the search query when switching pages.

        return view('User.booking', [
            'reservations' => $reservations,
            'seatMapReservations' => $this->seatMapReservations(),
            'search' => $search,
            'username' => $request->session()->get('username'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->normalizeSeatNumber($request);
        $this->applyMovieTheater($request);

        $data = $request->validate($this->rules($request));
        $data['user_id'] = $this->currentUserId($request);

        SeatReservation::create($data);

        return redirect()
            ->route('reservations.index')
            ->with('status', 'Seat reservation created.')
            ->withInput([
                'movie_title' => $data['movie_title'],
                'theater' => $data['theater'],
                'show_date' => $data['show_date'],
                'show_time' => $data['show_time'],
            ]);
    }

    public function edit(Request $request, SeatReservation $seatReservation): View
    {
        return view('reservations.edit', [
            'reservation' => $seatReservation,
            'seatMapReservations' => $this->seatMapReservations(),
            'username' => $request->session()->get('username'),
        ]);
    }

    public function update(Request $request, SeatReservation $seatReservation): RedirectResponse
    {
        $this->normalizeSeatNumber($request);
        $this->applyMovieTheater($request);

        $seatReservation->update($request->validate($this->rules($request, $seatReservation)));
        // Validate the new data, then update the existing reservation row.

        return redirect()->route('reservations.index')->with('status', 'Seat reservation updated.');
    }

    public function destroy(SeatReservation $seatReservation): RedirectResponse
    {
        $seatReservation->delete();

        return redirect()->route('reservations.index')->with('status', 'Seat reservation deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(Request $request, ?SeatReservation $reservation = null): array
    {
        $uniqueSeat = Rule::unique('seat_reservations', 'seat_number')
            ->where('theater', $request->input('theater'))
            ->where('movie_title', $request->input('movie_title'))
            ->where('show_date', $request->input('show_date'))
            ->where('show_time', $request->input('show_time'));

        if ($reservation) {
            $uniqueSeat->ignore($reservation->id);
        }

        return [
            'customer_name' => ['required', 'string', 'max:80'],
            'movie_title' => ['required', 'string', Rule::in(config('cinema.movies'))],
            'theater' => ['required', 'string', Rule::in(config('cinema.movie_theaters'))],
            'seat_number' => ['required', 'string', 'max:2', 'regex:/^[ABC][1-6]$/', $uniqueSeat],
            'show_date' => ['required', 'date', 'after_or_equal:today'],
            'show_time' => ['required', 'date_format:H:i', Rule::in(config('cinema.show_times'))],
        ];
    }

    // The logged-in identity is the session username (the project allows a
    // hardcoded "user" login with no database row), so user_id is optional.
    private function currentUserId(Request $request): ?int
    {
        return User::where('username', $request->session()->get('username'))->value('id');
    }

    private function normalizeSeatNumber(Request $request): void
    {
        $request->merge([
            'seat_number' => strtoupper(trim((string) $request->input('seat_number'))),
        ]);
    }

    private function applyMovieTheater(Request $request): void
    {
        $movieTheaters = config('cinema.movie_theaters');
        $movieTitle = (string) $request->input('movie_title');

        $request->merge([
            'theater' => $movieTheaters[$movieTitle] ?? null,
            // Theater is automatically selected based on the chosen movie.
        ]);
    }

    private function seatMapReservations()
    {
        return SeatReservation::query()
            ->get(['id', 'movie_title', 'theater', 'seat_number', 'show_date', 'show_time'])
            ->map(fn (SeatReservation $reservation) => [
                'id' => $reservation->id,
                'movie_title' => $reservation->movie_title,
                'theater' => $reservation->theater,
                'seat_number' => strtoupper($reservation->seat_number),
                'show_date' => $reservation->show_date->format('Y-m-d'),
                'show_time' => substr($reservation->show_time, 0, 5),
            ]);
    }
}
