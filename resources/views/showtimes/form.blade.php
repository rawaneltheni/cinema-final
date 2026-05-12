<x-layouts.app :title="$showtime->exists ? 'Edit Showtime' : 'Add Showtime'" :username="$username">
    <main class="container">
        <section class="panel">
            <div class="toolbar">
                <div>
                    <h1>{{ $showtime->exists ? 'Edit Showtime' : 'Add Showtime' }}</h1>
                    <p class="muted">Enter the screening schedule details.</p>
                </div>
                <a class="button secondary" href="{{ route('showtimes.index') }}">Back</a>
            </div>

            <form action="{{ $showtime->exists ? route('showtimes.update', $showtime) : route('showtimes.store') }}" method="POST">
                @csrf
                @if ($showtime->exists)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="field">
                        <label for="movie_title">Movie Title</label>
                        <input id="movie_title" name="movie_title" maxlength="100" value="{{ old('movie_title', $showtime->movie_title) }}">
                        @error('movie_title') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="genre">Genre</label>
                        <input id="genre" name="genre" maxlength="50" value="{{ old('genre', $showtime->genre) }}">
                        @error('genre') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label for="hall_number">Hall Number</label>
                        <input id="hall_number" name="hall_number" type="number" min="1" value="{{ old('hall_number', $showtime->hall_number) }}">
                        @error('hall_number') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="show_date">Show Date</label>
                        <input id="show_date" name="show_date" type="date" value="{{ old('show_date', optional($showtime->show_date)->format('Y-m-d')) }}">
                        @error('show_date') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label for="start_time">Start Time</label>
                        <input id="start_time" name="start_time" type="time" value="{{ old('start_time', $showtime->start_time ? substr($showtime->start_time, 0, 5) : '') }}">
                        @error('start_time') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="end_time">End Time</label>
                        <input id="end_time" name="end_time" type="time" value="{{ old('end_time', $showtime->end_time ? substr($showtime->end_time, 0, 5) : '') }}">
                        @error('end_time') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="field">
                        <label for="available_seats">Available Seats</label>
                        <input id="available_seats" name="available_seats" type="number" min="0" value="{{ old('available_seats', $showtime->available_seats) }}">
                        @error('available_seats') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label for="ticket_price">Ticket Price</label>
                        <input id="ticket_price" name="ticket_price" type="number" min="0.01" max="9999.99" step="0.01" value="{{ old('ticket_price', $showtime->ticket_price) }}">
                        @error('ticket_price') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="field">
                    <label for="movie_status">Movie Status</label>
                    <select id="movie_status" name="movie_status">
                        <option value="">Select status</option>
                        <option value="Showing" @selected(old('movie_status', $showtime->movie_status) === 'Showing')>Showing</option>
                        <option value="Coming Soon" @selected(old('movie_status', $showtime->movie_status) === 'Coming Soon')>Coming Soon</option>
                    </select>
                    @error('movie_status') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="actions">
                    <button class="button primary" type="submit">{{ $showtime->exists ? 'Update Showtime' : 'Create Showtime' }}</button>
                </div>
            </form>
        </section>
    </main>
</x-layouts.app>
