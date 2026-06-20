@php
    $reservation ??= null;
    $seatMapReservations ??= collect();
    $selectedSeat = strtoupper(old('seat_number', $reservation?->seat_number ?? ''));
    $selectedMovie = old('movie_title', $reservation?->movie_title);
    $movieTheaters = config('cinema.movie_theaters');
    $selectedTheater = old('theater', $reservation?->theater ?? ($movieTheaters[$selectedMovie] ?? ''));
    $selectedShowDate = old('show_date', $reservation?->show_date?->format('Y-m-d') ?? today()->format('Y-m-d'));
    $selectedShowTime = old('show_time', $reservation ? substr($reservation->show_time, 0, 5) : null);
    $seatRows = ['A', 'B', 'C'];
    $seatColumns = range(1, 6);
    $movies = config('cinema.movies');
    $showTimes = config('cinema.show_times');
@endphp

<label class="block">
    <span class="text-sm font-semibold text-neutral-200">Customer name</span>
    <input
        name="customer_name"
        value="{{ old('customer_name', $reservation?->customer_name) }}"
        required
        maxlength="80"
        class="mt-2 w-full rounded-2xl border border-white/10 bg-neutral-900/90 px-4 py-3 text-white outline-none transition focus:border-[#E50914] focus:ring-4 focus:ring-red-600/20"
        placeholder="Aisha Morgan"
    >
</label>

<label class="block">
    <span class="text-sm font-semibold text-neutral-200">Movie name</span>
    <select
        name="movie_title"
        required
        data-movie-select
        data-movie-theaters='{!! collect($movieTheaters)->toJson(JSON_HEX_APOS | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT) !!}'
        class="mt-2 w-full rounded-2xl border border-white/10 bg-neutral-900/90 px-4 py-3 text-white outline-none transition focus:border-[#E50914] focus:ring-4 focus:ring-red-600/20"
    >
        <option value="" class="bg-black">Select a movie</option>
        @foreach ($movies as $movie)
            <option value="{{ $movie }}" @selected($selectedMovie === $movie) class="bg-black">
                {{ $movie }}
            </option>
        @endforeach
    </select>
</label>

<div class="grid gap-4 sm:grid-cols-2">
    <label class="block">
        <span class="text-sm font-semibold text-neutral-200">Theater</span>
        <input
            name="theater"
            value="{{ $selectedTheater }}"
            required
            readonly
            maxlength="60"
            data-theater-input
            class="mt-2 w-full rounded-2xl border border-red-600/30 bg-red-950/30 px-4 py-3 text-red-100 outline-none transition focus:border-[#E50914] focus:ring-4 focus:ring-red-600/20"
            placeholder="Auto-filled by movie"
        >
        <span class="mt-2 block text-xs text-neutral-400">Theater is assigned automatically from the selected movie.</span>
    </label>

    <div class="rounded-2xl border border-red-600/30 bg-red-950/30 px-4 py-3">
        <span class="text-sm font-semibold text-neutral-200">Selected seat</span>
        <input
            type="hidden"
            name="seat_number"
            value="{{ $selectedSeat }}"
            required
            data-seat-input
        >
        <p class="mt-2 text-2xl font-black text-red-200" data-selected-seat-label>
            {{ $selectedSeat ?: 'Choose below' }}
        </p>
    </div>
</div>

<div class="grid gap-4 sm:grid-cols-2">
    <label class="block">
        <span class="text-sm font-semibold text-neutral-200">Show date</span>
        <input
            type="date"
            name="show_date"
            value="{{ $selectedShowDate }}"
            required
            class="mt-2 w-full rounded-2xl border border-white/10 bg-neutral-900/90 px-4 py-3 text-white outline-none transition focus:border-[#E50914] focus:ring-4 focus:ring-red-600/20"
        >
    </label>

    <label class="block">
        <span class="text-sm font-semibold text-neutral-200">Show time</span>
        <select
            name="show_time"
            required
            class="mt-2 w-full rounded-2xl border border-white/10 bg-neutral-900/90 px-4 py-3 text-white outline-none transition focus:border-[#E50914] focus:ring-4 focus:ring-red-600/20"
        >
            <option value="" class="bg-black">Select show time</option>
            @foreach ($showTimes as $showTime)
                <option value="{{ $showTime }}" @selected($selectedShowTime === $showTime) class="bg-black">
                    {{ \Carbon\Carbon::createFromFormat('H:i', $showTime)->format('g:i A') }}
                </option>
            @endforeach
        </select>
    </label>
</div>

<section
    class="cinema-seat-map"
    data-seat-map
    data-current-reservation-id="{{ $reservation?->id }}"
    data-reservations='{!! $seatMapReservations->toJson(JSON_HEX_APOS | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT) !!}'
>
    <div class="cinema-curtain cinema-curtain-left" aria-hidden="true"></div>
    <div class="cinema-curtain cinema-curtain-right" aria-hidden="true"></div>

    <div class="relative z-10">
        <div class="mt-5 flex flex-wrap items-center justify-center gap-8 text-sm font-semibold text-neutral-300">
            <div class="flex flex-col items-center gap-1">
                <span class="cinema-seat-icon cinema-seat-available" aria-hidden="true"></span>
                Available
            </div>
            <div class="flex flex-col items-center gap-1">
                <span class="cinema-seat-icon cinema-seat-booked" aria-hidden="true"></span>
                Booked
            </div>
            <div class="flex flex-col items-center gap-1">
                <span class="cinema-seat-icon cinema-seat-selected" aria-hidden="true"></span>
                Selected
            </div>
        </div>

        <div class="mt-8 space-y-5">
            @foreach ($seatRows as $row)
                <div class="grid grid-cols-[1.5rem_repeat(6,minmax(2.25rem,1fr))] items-center gap-3 sm:grid-cols-[2rem_repeat(6,minmax(3rem,1fr))] sm:gap-5">
                    <div class="text-xl font-black text-neutral-200">{{ $row }}</div>
                    @foreach ($seatColumns as $column)
                        @php($seatCode = $row.$column)
                        <button
                            type="button"
                            class="cinema-seat-button {{ $column === 4 ? 'cinema-seat-aisle' : '' }}"
                            data-seat="{{ $seatCode }}"
                            data-state="{{ $selectedSeat === $seatCode ? 'selected' : 'available' }}"
                            aria-label="Seat {{ $seatCode }}"
                            aria-pressed="{{ $selectedSeat === $seatCode ? 'true' : 'false' }}"
                        >
                            <span class="cinema-seat-icon" aria-hidden="true"></span>
                            <span class="sr-only">Seat {{ $seatCode }}</span>
                        </button>
                    @endforeach
                </div>
            @endforeach

            <div class="grid grid-cols-[1.5rem_repeat(6,minmax(2.25rem,1fr))] gap-3 pt-1 text-center text-xl font-black text-neutral-200 sm:grid-cols-[2rem_repeat(6,minmax(3rem,1fr))] sm:gap-5">
                <div></div>
                @foreach ($seatColumns as $column)
                    <div class="{{ $column === 4 ? 'cinema-seat-aisle' : '' }}">{{ $column }}</div>
                @endforeach
            </div>
        </div>

        <div class="mt-8 grid gap-3 text-center text-base font-semibold text-neutral-300 sm:grid-cols-3 sm:text-lg">
            <p>Booked seats: <span data-booked-count>0</span></p>
            <p>Available seats: <span data-available-count>18</span></p>
            <p>Total seats: <span data-total-count>18</span></p>
        </div>
    </div>
</section>

<p class="text-xs leading-5 text-neutral-400">
    Pick the movie, theater, date, and time first; the map will mark seats already booked for that exact show. Valid seats are A1-C6.
</p>

<button class="w-full rounded-2xl bg-[#E50914] px-5 py-3 font-black text-white shadow-lg shadow-red-950/60 transition hover:bg-red-700">
    {{ $buttonText }}
</button>
