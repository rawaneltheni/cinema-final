<x-admin::layouts.app title="Booking Management" :username="$username">
    <main class="container booking-container">
        <section class="panel">
            <div class="toolbar">
                <div>
                    <h1>Booking Management</h1>
                    <p class="muted">View all bookings, confirm or cancel requests, check booked seats, and search by customer.</p>
                </div>
            </div>

            <div class="stats">
                <div class="stat">
                    <span>Total Bookings</span>
                    <strong>{{ $totalBookings }}</strong>
                </div>
                <div class="stat">
                    <span>Pending Review</span>
                    <strong>{{ $pendingBookings }}</strong>
                </div>
                <div class="stat">
                    <span>Paid Bookings</span>
                    <strong>{{ $paidBookings }}</strong>
                </div>
            </div>

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            <form class="search" action="{{ route('bookings.index') }}" method="GET">
                <input name="search" value="{{ $search }}" placeholder="Search by customer, email, film, or seat">
                <button class="button secondary" type="submit">Search</button>
                @if ($search)
                    <a class="button secondary" href="{{ route('bookings.index') }}">Clear</a>
                @endif
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Film Booked</th>
                        <th>Chair Type</th>
                        <th>Seat Numbers</th>
                        <th>Snacks</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Accept / Reject</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr>
                            <td data-label="Customer">
                                <strong>{{ $booking->customer_name }}</strong>
                                <span class="table-subtext">{{ $booking->customer_email }}</span>
                            </td>
                            <td data-label="Film Booked">
                                <div class="movie-cell">
                                    @if ($booking->showtime?->image)
                                        <img class="movie-poster" src="{{ $booking->showtime->image }}" alt="{{ $booking->showtime->movie_title }} poster" loading="lazy">
                                    @else
                                        <span class="poster-empty">No poster</span>
                                    @endif
                                    <div>
                                        <strong>{{ $booking->showtime?->movie_title ?? 'Deleted showtime' }}</strong>
                                        @if ($booking->showtime)
                                            <span class="table-subtext">Hall {{ $booking->showtime->hall_number }} / {{ $booking->showtime->show_date->format('Y-m-d') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td data-label="Chair Type">
                                <span class="badge {{ $booking->chair_type === 'VIP' ? 'badge-vip' : 'badge-premium' }}">
                                    {{ $booking->chair_type }}
                                </span>
                            </td>
                            <td data-label="Seat Numbers">
                                <strong>{{ $booking->seat_numbers ?: 'Not assigned' }}</strong>
                                <span class="table-subtext">{{ $booking->chair_count }} {{ $booking->chair_count === 1 ? 'chair' : 'chairs' }}</span>
                            </td>
                            <td data-label="Snacks">{{ $booking->snacks ?: 'No snacks' }}</td>
                            <td data-label="Payment">
                                <strong>{{ number_format($booking->payment_amount, 2) }}</strong>
                                <span class="table-subtext">{{ ucfirst($booking->payment_status) }}{{ $booking->payment_method ? ' / '.$booking->payment_method : '' }}</span>
                            </td>
                            <td data-label="Status">
                                <span class="badge badge-booking-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                            </td>
                            <td data-label="Accept / Reject">
                                <div class="table-actions">
                                    <form action="{{ route('bookings.status', $booking) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="accepted">
                                        <button class="button primary" type="submit" @disabled($booking->status === 'accepted')>Accept</button>
                                    </form>
                                    <form action="{{ route('bookings.status', $booking) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="button danger" type="submit" @disabled($booking->status === 'rejected')>Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="empty" colspan="8">No bookings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</x-admin::layouts.app>
