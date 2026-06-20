@extends('layouts.app')

@section('title', 'Dashboard | Cinema Reservation')

@section('content')
    <header class="flex flex-col gap-5 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-red-500">Reservation Dashboard</p>
            <h1 class="mt-2 text-4xl font-black text-white">Tonight's seats</h1>
            <p class="mt-2 text-neutral-300">Signed in as <strong class="text-red-300">{{ $username }}</strong>.</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="rounded-full border border-white/15 px-5 py-2.5 font-semibold text-neutral-100 transition hover:border-[#E50914] hover:text-red-300">
                Logout
            </button>
        </form>
    </header>

    @if (session('status'))
        <div class="mt-6 rounded-2xl border border-emerald-300/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-6 rounded-2xl border border-red-300/30 bg-red-500/10 px-4 py-3 text-sm text-red-100">
            <strong class="font-black">Please fix:</strong> {{ $errors->first() }}
        </div>
    @endif

    <section class="grid gap-6 py-8 lg:grid-cols-[0.8fr_1.2fr]">
        <div class="rounded-[2rem] border border-white/10 bg-neutral-950/80 p-6 shadow-2xl shadow-black/50">
            <p class="text-sm uppercase tracking-[0.28em] text-red-500">Insert Record</p>
            <h2 class="mt-2 text-2xl font-black text-white">New reservation</h2>

            <form method="POST" action="{{ route('reservations.store') }}" class="mt-6 space-y-4">
                @csrf
                @include('reservations.partials.form', ['reservation' => null, 'buttonText' => 'Reserve Seat', 'seatMapReservations' => $seatMapReservations])
            </form>
        </div>

        <div class="rounded-[2rem] border border-white/10 bg-black/75 p-6 shadow-2xl shadow-black/50">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-[0.28em] text-red-500">Search Records</p>
                    <h2 class="mt-2 text-2xl font-black text-white">Reservation list</h2>
                </div>

                <form method="GET" action="{{ route('reservations.index') }}" class="flex gap-2">
                    <input
                        name="search"
                        value="{{ $search }}"
                        class="w-full rounded-full border border-white/10 bg-neutral-900/90 px-4 py-2.5 text-white outline-none transition focus:border-[#E50914] sm:w-64"
                        placeholder="Movie, customer, seat..."
                    >
                    <button class="rounded-full bg-[#E50914] px-5 py-2.5 font-black text-white transition hover:bg-red-700">Search</button>
                </form>
            </div>

            <div class="mt-6 overflow-hidden rounded-3xl border border-white/10">
                <table class="w-full min-w-[760px] border-collapse text-left text-sm">
                    <thead class="bg-red-950/40 text-xs uppercase tracking-[0.2em] text-red-200">
                        <tr>
                            <th class="px-4 py-4">Customer</th>
                            <th class="px-4 py-4">Movie</th>
                            <th class="px-4 py-4">Theater</th>
                            <th class="px-4 py-4">Seat</th>
                            <th class="px-4 py-4">Show</th>
                            <th class="px-4 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($reservations as $reservation)
                            <tr class="bg-neutral-950/60 text-neutral-200">
                                <td class="px-4 py-4 font-semibold text-white">{{ $reservation->customer_name }}</td>
                                <td class="px-4 py-4">{{ $reservation->movie_title }}</td>
                                <td class="px-4 py-4">{{ $reservation->theater }}</td>
                                <td class="px-4 py-4">
                                    <span class="rounded-full bg-[#E50914] px-3 py-1 font-black text-white">{{ $reservation->seat_number }}</span>
                                </td>
                                <td class="px-4 py-4">{{ $reservation->show_date->format('M d, Y') }} at {{ substr($reservation->show_time, 0, 5) }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('reservations.edit', $reservation) }}" class="rounded-full border border-red-500/60 px-3 py-1.5 font-semibold text-red-200 transition hover:bg-[#E50914] hover:text-white">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('reservations.destroy', $reservation) }}" onsubmit="return confirm('Delete this reservation?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-full border border-white/15 px-3 py-1.5 font-semibold text-neutral-200 transition hover:bg-white hover:text-black">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-neutral-400">
                                    No reservations found. Add the first seat and the projector starts humming.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $reservations->links() }}
            </div>
        </div>
    </section>
@endsection
