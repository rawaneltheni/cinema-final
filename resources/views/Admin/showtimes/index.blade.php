{{-- Dashboard page: receives showtimes, search text, username, and statistics from ShowtimeController@index. --}}
<x-admin::layouts.app title="Cinema Showtime Management System" :username="$username">
    <main class="container">
        <section class="panel">
            {{-- Top dashboard toolbar with page title and add button. --}}
            <div class="toolbar">
                <div>
                    <h1>Showtime Dashboard</h1>
                    <p class="muted">Welcome {{ $username }}. Manage schedules, halls, prices, seats, and movie status.</p>
                </div>
                {{-- Opens the create form for a new showtime. --}}
                <a class="button primary" href="{{ route('showtimes.create') }}">Add Showtime</a>
            </div>

            {{-- Dashboard statistics calculated in the controller. --}}
            <div class="stats">
                <div class="stat">
                    <span>Total Movies</span>
                    <strong>{{ $totalMovies }}</strong>
                </div>
                <div class="stat">
                    <span>Total Halls</span>
                    <strong>{{ $totalHalls }}</strong>
                </div>
                <div class="stat">
                    <span>Available Seats</span>
                    <strong>{{ $availableSeats }}</strong>
                </div>
            </div>

            {{-- Show success messages after add, update, or delete actions. --}}
            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            {{-- Search form sends a GET request so the search text appears in the URL. --}}
            <form class="search" action="{{ route('showtimes.index') }}" method="GET">
                <input name="search" value="{{ $search }}" placeholder="Search title, genre, hall, or date">
                <button class="button secondary" type="submit">Search</button>
                {{-- Clear button appears only when search is active. --}}
                @if ($search)
                    <a class="button secondary" href="{{ route('showtimes.index') }}">Clear</a>
                @endif
            </form>

            {{-- Table of showtime records returned by the controller. --}}
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Movie</th>
                        <th>Genre</th>
                        <th>Hall</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Seats</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop through showtimes, or show an empty message if no records exist. --}}
                    @forelse ($showtimes as $showtime)
                        <tr>
                            <td data-label="ID">{{ $showtime->show_id }}</td>
                            <td data-label="Movie">
                                <div class="movie-cell">
                                    @if ($showtime->image)
                                        <img class="movie-poster" src="{{ $showtime->image }}" alt="{{ $showtime->movie_title }} poster" loading="lazy">
                                    @else
                                        <span class="poster-empty">No poster</span>
                                    @endif
                                    <strong>{{ $showtime->movie_title }}</strong>
                                </div>
                            </td>
                            <td data-label="Genre">{{ $showtime->genre }}</td>
                            <td data-label="Hall">{{ $showtime->hall_number }}</td>
                            <td data-label="Date">{{ $showtime->show_date->format('Y-m-d') }}</td>
                            <td data-label="Time">{{ substr($showtime->start_time, 0, 5) }} - {{ substr($showtime->end_time, 0, 5) }}</td>
                            <td data-label="Seats">{{ $showtime->available_seats }}</td>
                            <td data-label="Price">{{ number_format($showtime->ticket_price, 2) }}</td>
                            <td data-label="Status">
                                {{-- Badge color changes depending on the movie status. --}}
                                <span class="badge {{ $showtime->movie_status === 'Showing' ? 'badge-showing' : 'badge-coming' }}">
                                    {{ $showtime->movie_status }}
                                </span>
                            </td>
                            <td data-label="Actions">
                                <div class="table-actions">
                                    {{-- Opens the edit form for this record. --}}
                                    <a class="button secondary" href="{{ route('showtimes.edit', $showtime) }}">Edit</a>
                                    {{-- Delete form uses DELETE method and asks for confirmation first. --}}
                                    <form action="{{ route('showtimes.destroy', $showtime) }}" method="POST" onsubmit="return confirm('Delete this showtime?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="10">No showtimes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</x-admin::layouts.app>
