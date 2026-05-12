<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShowtimeController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $showtimes = Showtime::query()
            ->when($search, function ($query) use ($search) {
                $query->where('movie_title', 'like', "%{$search}%")
                    ->orWhere('genre', 'like', "%{$search}%")
                    ->orWhere('hall_number', 'like', "%{$search}%")
                    ->orWhere('show_date', 'like', "%{$search}%");
            })
            ->orderBy('show_date')
            ->orderBy('start_time')
            ->paginate(8)
            ->withQueryString();

        return view('showtimes.index', [
            'showtimes' => $showtimes,
            'search' => $search,
            'username' => $request->session()->get('username'),
            'totalMovies' => Showtime::distinct('movie_title')->count('movie_title'),
            'totalHalls' => Showtime::distinct('hall_number')->count('hall_number'),
            'availableSeats' => Showtime::sum('available_seats'),
        ]);
    }

    public function create(): View
    {
        return view('showtimes.form', [
            'showtime' => new Showtime(),
            'username' => session('username'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Showtime::create($this->validatedShowtime($request));

        return redirect()->route('showtimes.index')->with('status', 'Showtime added successfully.');
    }

    public function edit(Showtime $showtime): View
    {
        return view('showtimes.form', [
            'showtime' => $showtime,
            'username' => session('username'),
        ]);
    }

    public function update(Request $request, Showtime $showtime): RedirectResponse
    {
        $showtime->update($this->validatedShowtime($request));

        return redirect()->route('showtimes.index')->with('status', 'Showtime updated successfully.');
    }

    public function destroy(Showtime $showtime): RedirectResponse
    {
        $showtime->delete();

        return redirect()->route('showtimes.index')->with('status', 'Showtime deleted successfully.');
    }

    private function validatedShowtime(Request $request): array
    {
        return $request->validate([
            'movie_title' => ['required', 'string', 'max:100'],
            'genre' => ['required', 'string', 'max:50'],
            'hall_number' => ['required', 'integer', 'min:1'],
            'show_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'available_seats' => ['required', 'integer', 'min:0'],
            'ticket_price' => ['required', 'numeric', 'gt:0', 'max:9999.99'],
            'movie_status' => ['required', Rule::in(['Showing', 'Coming Soon'])],
        ]);
    }
}
