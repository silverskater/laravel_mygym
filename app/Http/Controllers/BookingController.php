<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\ScheduledClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function create()
    {
        $scheduledClasses = ScheduledClass::upcoming()
            ->with(['classType', 'instructor'])
            ->notBookedByUser(Auth::id())
            ->oldest('scheduled_at')->get();

        return view('member.booking', compact('scheduledClasses'));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        Auth::user()->bookings()->attach($request->validated()['scheduled_class_id'], [
            'status' => 'pending',
        ]);

        return redirect()->route('member.booking.index')
            ->with('success', 'You have successfully booked the class!');
    }

    public function index()
    {
        $bookings = Auth::user()->bookings()->upcoming()
            ->where('bookings.status', '!=', 'cancelled')
            ->with(['classType', 'instructor'])
            ->oldest('bookings.created_at')
            ->get();

        return view('member.upcoming')->with('bookings', $bookings);
    }

    public function destroy($id)
    {
        Auth::user()->bookings()->detach($id);

        return redirect()->route('member.booking.index')
            ->with('success', 'Booking cancelled successfully.');
    }
}
