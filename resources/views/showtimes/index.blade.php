<x-layouts.app title="Cinema Showtime Management System" :username="$username">
    <main class="container">
        <section class="panel">
            <div class="toolbar">
                <div>
                    <h1>Showtime Dashboard</h1>
                    <p class="muted">Welcome {{ $username }}. Manage schedules, halls, prices, seats, and movie status.</p>
                </div>
                <a class="button primary" href="{{ route('showtimes.create') }}">Add Showtime</a>
            </div>

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

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            <form class="search" action="{{ route('showtimes.index') }}" method="GET">
                <input name="search" value="{{ $search }}" placeholder="Search title, genre, hall, or date">
                <button class="button secondary" type="submit">Search</button>
                @if ($search)
                    <a class="button secondary" href="{{ route('showtimes.index') }}">Clear</a>
                @endif
            </form>

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
                    @forelse ($showtimes as $showtime)
                        <tr>
                            <td data-label="ID">{{ $showtime->show_id }}</td>
                            <td data-label="Movie">{{ $showtime->movie_title }}</td>
                            <td data-label="Genre">{{ $showtime->genre }}</td>
                            <td data-label="Hall">{{ $showtime->hall_number }}</td>
                            <td data-label="Date">{{ $showtime->show_date->format('Y-m-d') }}</td>
                            <td data-label="Time">{{ substr($showtime->start_time, 0, 5) }} - {{ substr($showtime->end_time, 0, 5) }}</td>
                            <td data-label="Seats">{{ $showtime->available_seats }}</td>
                            <td data-label="Price">{{ number_format($showtime->ticket_price, 2) }}</td>
                            <td data-label="Status">
                                <span class="badge {{ $showtime->movie_status === 'Showing' ? 'badge-showing' : 'badge-coming' }}">
                                    {{ $showtime->movie_status }}
                                </span>
                            </td>
                            <td data-label="Actions">
                                <div class="table-actions">
                                    <a class="button secondary" href="{{ route('showtimes.edit', $showtime) }}">Edit</a>
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

            <div class="pagination">
                {{ $showtimes->links() }}
            </div>
        </section>
    </main>
</x-layouts.app>
