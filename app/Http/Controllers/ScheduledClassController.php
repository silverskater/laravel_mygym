<?php

namespace App\Http\Controllers;

use App\Events\ClassCancelled;
use App\Http\Requests\StoreScheduledClassRequest;
use App\Models\ClassType;
use App\Models\ScheduledClass;
use Illuminate\Http\RedirectResponse;
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
    public function store(StoreScheduledClassRequest $request): RedirectResponse
    {
        ScheduledClass::create([
            'instructor_id' => Auth::id(),
            'class_type_id' => $request->validated()['class_type_id'],
            'scheduled_at' => $request->validated()['date'] . ' ' . $request->validated()['time'],
        ]);

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

        // Fire the ClassCancelled event.
        ClassCancelled::dispatch($schedule);

        $schedule->members()->detach();
        $schedule->delete();

        return redirect()->route('schedule.index');
    }
}
