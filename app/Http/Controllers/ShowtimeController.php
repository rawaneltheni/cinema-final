<?php

namespace App\Http\Controllers;

use App\Models\Showtime;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShowtimeController extends Controller
{
    // Display the dashboard with showtime records, search, and statistics.
    public function index(Request $request): View
    {
        // Read the search text from the URL query string.
        $search = $request->string('search')->toString();

        // Build the showtime query and apply search only when the user typed something.
        $showtimes = Showtime::query()
            ->when($search, function ($query) use ($search) {
                // Search by movie title, genre, hall number, or show date.
                $query->where('movie_title', 'like', "%{$search}%")
                    ->orWhere('genre', 'like', "%{$search}%")
                    ->orWhere('hall_number', 'like', "%{$search}%")
                    ->orWhere('show_date', 'like', "%{$search}%");
            })
            // Show the newest created records first so added movies appear immediately.
            ->latest('created_at')
            ->latest('show_id')
            ->get();

        // Send all dashboard data to the showtimes index view.
        return view('Admin.showtimes.index', [
            'showtimes' => $showtimes,
            'search' => $search,
            'username' => $request->session()->get('username'),
            // Count unique movie titles for the dashboard statistic.
            'totalMovies' => Showtime::distinct('movie_title')->count('movie_title'),
            // Count unique hall numbers for the dashboard statistic.
            'totalHalls' => Showtime::distinct('hall_number')->count('hall_number'),
            // Sum all available seats for the dashboard statistic.
            'availableSeats' => Showtime::sum('available_seats'),
        ]);
    }

    // Display showtimes in a monthly movie calendar.
    public function calendar(Request $request): View
    {
        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $latestShowDate = Showtime::max('show_date');
        $month = isset($validated['month'])
            ? CarbonImmutable::createFromFormat('Y-m', $validated['month'])->startOfMonth()
            : CarbonImmutable::parse($latestShowDate ?? now())->startOfMonth();

        $calendarStart = $month->startOfWeek(CarbonInterface::SUNDAY);
        $calendarEnd = $month->endOfMonth()->endOfWeek(CarbonInterface::SATURDAY);
        $calendarDays = collect(CarbonPeriod::create($calendarStart, $calendarEnd))
            ->map(fn ($day) => $day->toImmutable());

        $showtimesByDate = Showtime::query()
            ->whereBetween('show_date', [$calendarStart->toDateString(), $calendarEnd->toDateString()])
            ->orderBy('show_date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn (Showtime $showtime) => $showtime->show_date->format('Y-m-d'));

        return view('Admin.showtimes.calendar', [
            'month' => $month,
            'calendarDays' => $calendarDays,
            'showtimesByDate' => $showtimesByDate,
            'username' => $request->session()->get('username'),
        ]);
    }

    // Show an empty form for adding a new showtime.
    public function create(): View
    {
        return view('Admin.showtimes.form', [
            // A new empty model lets the Blade form know this is create mode.
            'showtime' => new Showtime,
            'username' => session('username'),
        ]);
    }

    // Save a new showtime after the form is submitted.
    public function store(Request $request): RedirectResponse
    {
        // Validate the input first, then insert the showtime into the database.
        Showtime::create($this->showtimeData($request));

        return redirect()->route('showtimes.index')->with('status', 'Showtime added successfully.');
    }

    // Show the form with an existing showtime for editing.
    public function edit(Showtime $showtime): View
    {
        return view('Admin.showtimes.form', [
            'showtime' => $showtime,
            'username' => session('username'),
        ]);
    }

    // Update an existing showtime after the edit form is submitted.
    public function update(Request $request, Showtime $showtime): RedirectResponse
    {
        // Validate the new values, then update the selected database record.
        $showtime->update($this->showtimeData($request, $showtime));

        return redirect()->route('showtimes.index')->with('status', 'Showtime updated successfully.');
    }

    // Delete one showtime record.
    public function destroy(Showtime $showtime): RedirectResponse
    {
        $this->deleteUploadedImage($showtime->image);
        $showtime->delete();

        return redirect()->route('showtimes.index')->with('status', 'Showtime deleted successfully.');
    }

    // Keep all showtime validation rules in one method so store and update use the same rules.
    private function showtimeData(Request $request, ?Showtime $showtime = null): array
    {
        $data = $request->validate([
            // Movie title and genre are required text fields with maximum lengths.
            'movie_title' => ['required', 'string', 'max:100'],
            // OMDb's Poster value is a full HTTP(S) URL stored in the image column.
            'image' => ['nullable', 'url:http,https', 'max:2048'],
            // A manually uploaded poster takes priority over an OMDb poster URL.
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'image_changed' => ['nullable', 'boolean'],
            'genre' => ['required', 'string', 'max:50'],
            // Hall number must be a positive integer.
            'hall_number' => ['required', 'integer', 'min:1'],
            // Show date must be a valid date.
            'show_date' => ['required', 'date'],
            // Times must be in HH:MM format.
            'start_time' => ['required', 'date_format:H:i'],
            // End time must be after the start time.
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            // Seats cannot be negative.
            'available_seats' => ['required', 'integer', 'min:0'],
            // Ticket price must be greater than zero and fit the database decimal size.
            'ticket_price' => ['required', 'numeric', 'gt:0', 'max:9999.99'],
            // Status is limited to the two allowed values used by the dropdown.
            'movie_status' => ['required', Rule::in(['Showing', 'Coming Soon'])],
        ]);

        $imageFile = $data['image_file'] ?? null;
        $imageChanged = (bool) ($data['image_changed'] ?? false);
        unset($data['image_file'], $data['image_changed']);

        if ($imageFile) {
            $this->deleteUploadedImage($showtime?->image);
            $path = $imageFile->store('movie-images', 'public');
            $data['image'] = Storage::disk('public')->url($path);
        } elseif (filled($data['image'] ?? null)) {
            if ($imageChanged && $showtime?->image !== $data['image']) {
                $this->deleteUploadedImage($showtime?->image);
            }
        } elseif ($imageChanged) {
            $this->deleteUploadedImage($showtime?->image);
            $data['image'] = null;
        } elseif ($showtime) {
            // No new file or OMDb poster was chosen, so retain the existing image.
            unset($data['image']);
        }

        // Status follows the schedule date so future movies always appear as Coming Soon.
        $data['movie_status'] = CarbonImmutable::parse($data['show_date'])->isAfter(CarbonImmutable::today())
            ? 'Coming Soon'
            : 'Showing';

        return $data;
    }

    private function deleteUploadedImage(?string $image): void
    {
        if (! $image) {
            return;
        }

        $path = parse_url($image, PHP_URL_PATH);
        $marker = '/storage/movie-images/';

        // Only delete images managed by this application, never remote OMDb images.
        if (! is_string($path) || ! Str::contains($path, $marker)) {
            return;
        }

        Storage::disk('public')->delete('movie-images/'.Str::after($path, $marker));
    }
}
