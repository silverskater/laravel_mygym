<?php

namespace App\Http\Controllers;

use App\Models\ClassType;
use App\Models\ScheduledClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduledClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $scheduledClasses = Auth::user()->scheduledClasses()->upcoming()->oldest('date_time')->get();
        $scheduledClasses = Auth::user()->scheduledClasses()->where('date_time', '>', now())->oldest('date_time')->get();
        return view('instructor.upcoming')->with('scheduledClasses', $scheduledClasses);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('instructor.schedule')->with('classTypes', ClassType::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'date_time' => $request->input('date') . ' ' . $request->input('time'),
            'instructor_id' => Auth::id(),
        ]);

        $values = $request->validate([
            'class_type_id' => 'required',
            'instructor_id' => 'required',
            'date_time' => 'required|unique:scheduled_classes,date_time|after:now',
        ]);

        ScheduledClass::create($values);


        return redirect()->route('schedule.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScheduledClass $schedule)
    {
        if (Auth::id() !== $schedule->instructor_id) {
            abort(403);
        }

        $schedule->delete();

        return redirect()->route('schedule.index');
    }
}
