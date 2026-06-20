@php
    $reservation ??= null;
    $accountName ??= '';
    $bookedSeats = collect($bookedSeats ?? [])->map(fn ($seat) => strtoupper($seat));
    $currentSeat = strtoupper(old('seat_number', $reservation?->seat_number ?? ''));
    $customerName = $reservation?->customer_name ?? $accountName;
    $seatRows = config('cinema.seat_rows');
    $seatColumns = config('cinema.seat_columns');
    $totalSeats = count($seatRows) * count($seatColumns);
    $bookedCount = $bookedSeats->count();
@endphp

<label class="block">
    <span class="text-sm font-semibold text-neutral-200">Customer name</span>
    <input
        name="customer_name"
        value="{{ $customerName }}"
        readonly
        maxlength="80"
        class="mt-2 w-full cursor-not-allowed rounded-2xl border border-white/10 bg-neutral-900/60 px-4 py-3 text-neutral-200 outline-none"
    >
    <span class="mt-2 block text-xs text-neutral-400">Booked under your account.</span>
</label>

<div class="rounded-2xl border border-red-600/30 bg-red-950/30 px-4 py-3">
    <span class="text-sm font-semibold text-neutral-200">Selected seat</span>
    <input
        type="hidden"
        name="seat_number"
        value="{{ $currentSeat }}"
        required
        data-seat-input
    >
    <p class="mt-2 text-2xl font-black text-red-200" data-selected-seat-label>
        {{ $currentSeat ?: 'Choose below' }}
    </p>
</div>

<section class="cinema-seat-map" data-seat-map>
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
                        @php
                            $seatCode = $row.$column;
                            $isBooked = $bookedSeats->contains($seatCode) && $seatCode !== $currentSeat;
                            $state = $isBooked ? 'booked' : ($seatCode === $currentSeat ? 'selected' : 'available');
                        @endphp
                        <button
                            type="button"
                            class="cinema-seat-button {{ $column === 4 ? 'cinema-seat-aisle' : '' }}"
                            data-seat="{{ $seatCode }}"
                            data-state="{{ $state }}"
                            @disabled($isBooked)
                            aria-label="Seat {{ $seatCode }} {{ $state }}"
                            aria-pressed="{{ $seatCode === $currentSeat ? 'true' : 'false' }}"
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
            <p>Booked seats: <span data-booked-count>{{ $bookedCount }}</span></p>
            <p>Available seats: <span data-available-count>{{ $totalSeats - $bookedCount }}</span></p>
            <p>Total seats: <span data-total-count>{{ $totalSeats }}</span></p>
        </div>
    </div>
</section>

<p class="text-xs leading-5 text-neutral-400">
    Pick any open seat in the grid (A1-C6). Greyed seats are already booked for this show.
</p>

<button class="w-full rounded-2xl bg-[#E50914] px-5 py-3 font-black text-white shadow-lg shadow-red-950/60 transition hover:bg-red-700">
    {{ $buttonText }}
</button>
