<?php

namespace App\Http\Controllers;

use App\Models\ScheduledClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function create()
    {
        // $scheduledClasses = ScheduledClass::where('status', 'scheduled')->get();
        $scheduledClasses = ScheduledClass::upcoming()
            ->with(['classType', 'instructor'])
            ->notBookedByUser(Auth::id())
            ->oldest('scheduled_at')->get();

        return view('member.booking', compact('scheduledClasses'));
    }

    public function store(Request $request)
    {
        Auth::user()->bookings()->attach($request->input('scheduled_class_id'), [
            'status' => 'pending',
        ]);

        return redirect()->route('member.booking.index')
            ->with('success', 'Booking created successfully.');
    }

    public function index()
    {
        $bookings = Auth::user()->bookings()->upcoming()
            ->where('bookings.status', '!=', 'cancelled')
            // ->wherePivot('status', '!=', 'cancelled')
            // ->whereHas('scheduledClass', function ($query) {
            //     $query->where('scheduled_at', '>=', now());
            // })
            ->with(['classType', 'instructor'])
            ->oldest('bookings.created_at')
            ->get();

        // return view('bookings.index', compact('bookings'));
        return view('member.upcoming')->with('bookings', $bookings);
    }

    public function destroy($id)
    {
        Auth::user()->bookings()->detach($id);

        return redirect()->route('member.booking.index')
            ->with('success', 'Booking cancelled successfully.');
    }
}
