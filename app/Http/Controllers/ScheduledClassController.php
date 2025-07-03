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
        $scheduledClasses = Auth::user()->scheduledClasses()
            ->upcoming()
            ->with('classType')
            ->oldest('scheduled_at')
            ->get();
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
            'scheduled_at' => $request->input('date') . ' ' . $request->input('time'),
            'instructor_id' => Auth::id(),
        ]);

        $values = $request->validate([
            'class_type_id' => 'required',
            'instructor_id' => 'required',
            'scheduled_at' => 'required|unique:scheduled_classes,scheduled_at|after:now',
        ]);

        ScheduledClass::create($values);


        return redirect()->route('schedule.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScheduledClass $schedule)
    {
        if (Auth::user()->cannot('delete', $schedule)) {
            abort(403);
        }

        $schedule->delete();

        return redirect()->route('schedule.index');
    }
}
