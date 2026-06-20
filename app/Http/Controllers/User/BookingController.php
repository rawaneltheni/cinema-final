<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SeatReservationController;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Request $request, Movie $movie): View
    {
        // Render the ported reservation dashboard at the booking route.
        // (To be tailored to the selected movie in a follow-up.)
        return app(SeatReservationController::class)->index($request);
    }
}
