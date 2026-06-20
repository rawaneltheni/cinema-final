@extends('layouts.app')

@section('title', 'Book '.$movie->movie_title.' | Cinema')

@php
    $imageUrl = $movie->image && str_starts_with($movie->image, 'http')
        ? $movie->image
        : ($movie->image ? asset($movie->image) : null);
    $isBookable = $movie->movie_status === 'Showing' && $movie->available_seats > 0;
@endphp

@section('content')
    <header class="flex flex-col gap-5 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-red-500">Booking</p>
            <h1 class="mt-2 text-4xl font-black text-white">{{ $movie->movie_title }}</h1>
            <p class="mt-2 text-neutral-300">Signed in as <strong class="text-red-300">{{ $username }}</strong>.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('user.home') }}" class="rounded-full border border-white/15 px-5 py-2.5 font-semibold text-neutral-100 transition hover:border-[#E50914] hover:text-red-300">
                Back to Movies
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="rounded-full border border-white/15 px-5 py-2.5 font-semibold text-neutral-100 transition hover:border-[#E50914] hover:text-red-300">
                    Logout
                </button>
            </form>
        </div>
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
            <div class="overflow-hidden rounded-3xl border border-white/10 bg-neutral-900">
                @if ($imageUrl)
                    <img src="{{ $imageUrl }}" alt="{{ $movie->movie_title }} poster" class="aspect-[2/3] w-full object-cover">
                @else
                    <div class="grid aspect-[2/3] place-items-center text-center font-black text-neutral-500">No poster available</div>
                @endif
            </div>

            <dl class="mt-6 space-y-3 text-sm">
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-neutral-400">Genre</dt>
                    <dd class="font-semibold text-white">{{ $movie->genre }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-neutral-400">Theater</dt>
                    <dd class="font-semibold text-white">Hall {{ $movie->hall_number }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-neutral-400">Date</dt>
                    <dd class="font-semibold text-white">{{ $movie->show_date->format('M j, Y') }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-neutral-400">Time</dt>
                    <dd class="font-semibold text-white">{{ substr($movie->start_time, 0, 5) }} - {{ substr($movie->end_time, 0, 5) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-neutral-400">Ticket price</dt>
                    <dd class="font-semibold text-white">${{ number_format($movie->ticket_price, 2) }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4">
                    <dt class="text-neutral-400">Seats left</dt>
                    <dd class="font-semibold text-red-300">{{ $movie->available_seats }}</dd>
                </div>
            </dl>
        </div>

        <div class="rounded-[2rem] border border-white/10 bg-black/75 p-6 shadow-2xl shadow-black/50">
            <p class="text-sm uppercase tracking-[0.28em] text-red-500">Reserve a seat</p>
            <h2 class="mt-2 text-2xl font-black text-white">Choose your seat</h2>

            @if ($isBookable)
                <form method="POST" action="{{ route('movies.booking.store', $movie) }}" class="mt-6 space-y-4">
                    @csrf
                    @include('reservations.partials.form', ['reservation' => null, 'buttonText' => 'Reserve Seat', 'bookedSeats' => $bookedSeats, 'accountName' => $accountName])
                </form>
            @else
                <div class="mt-6 rounded-2xl border border-white/10 bg-neutral-900/70 px-4 py-6 text-center text-neutral-300">
                    @if ($movie->movie_status !== 'Showing')
                        This movie is not currently showing.
                    @else
                        Sold out — no seats remaining for this show.
                    @endif
                </div>
            @endif
        </div>
    </section>
@endsection
