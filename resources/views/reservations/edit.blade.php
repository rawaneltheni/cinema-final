@extends('layouts.app')

@section('title', 'Edit Reservation | Cinema Reservation')

@section('content')
    <header class="flex flex-col gap-5 border-b border-white/10 pb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.35em] text-red-500">Update Record</p>
            <h1 class="mt-2 text-4xl font-black text-white">Edit seat {{ $reservation->seat_number }}</h1>
            <p class="mt-2 text-neutral-300">Signed in as <strong class="text-red-300">{{ $username }}</strong>.</p>
        </div>

        <a href="{{ route('reservations.index') }}" class="rounded-full border border-white/15 px-5 py-2.5 text-center font-semibold text-neutral-100 transition hover:border-[#E50914] hover:text-red-300">
            Back to Dashboard
        </a>
    </header>

    @if ($errors->any())
        <div class="mt-6 rounded-2xl border border-red-300/30 bg-red-500/10 px-4 py-3 text-sm text-red-100">
            <strong class="font-black">Please fix:</strong> {{ $errors->first() }}
        </div>
    @endif

    <section class="mx-auto w-full max-w-2xl py-8">
        <div class="rounded-[2rem] border border-white/10 bg-neutral-950/80 p-6 shadow-2xl shadow-black/50">
            <form method="POST" action="{{ route('reservations.update', $reservation) }}" class="space-y-4">
                @csrf
                @method('PUT')
                @include('reservations.partials.form', ['reservation' => $reservation, 'buttonText' => 'Update Reservation', 'seatMapReservations' => $seatMapReservations])
            </form>
        </div>
    </section>
@endsection
