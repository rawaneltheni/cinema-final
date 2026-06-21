<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $bookings = Booking::query()
            ->with('showtime')
            ->when($search, function ($query) use ($search) {
                $query->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('seat_numbers', 'like', "%{$search}%")
                    ->orWhereHas('showtime', function ($query) use ($search) {
                        $query->where('movie_title', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->get();

        return view('Admin.bookings.index', [
            'bookings' => $bookings,
            'search' => $search,
            'username' => $request->session()->get('username'),
            'totalBookings' => Booking::count(),
            'pendingBookings' => Booking::where('status', 'pending')->count(),
            'paidBookings' => Booking::where('payment_status', 'paid')->count(),
        ]);
    }

    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['accepted', 'rejected'])],
        ]);

        $booking->update($data);

        return redirect()
            ->route('bookings.index')
            ->with('status', 'Booking '.$data['status'].' successfully.');
    }
}
