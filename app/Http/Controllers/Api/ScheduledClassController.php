<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScheduledClass;
use App\Models\ClassType;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduledClassController extends Controller
{
    public function index(Request $request)
    {
        $query = ScheduledClass::query();
        if ($request->has('instructor_id')) {
            $query->where('instructor_id', $request->instructor_id);
        }
        if ($request->has('date')) {
            $query->whereDate('scheduled_at', $request->date);
        }
        return $query->get();
    }

    public function show($id)
    {
        $scheduledClass = ScheduledClass::find($id);
        if (!$scheduledClass) {
            return response()->json(['message' => 'Resource not found.'], 404);
        }
        return $scheduledClass;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_type_id' => 'required|exists:class_types,id',
            'instructor_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date',
            'capacity' => 'integer|min:1',
            'status' => 'in:scheduled,completed,cancelled',
            'location' => 'string|nullable',
            'description' => 'string|nullable',
        ]);
        $scheduledClass = ScheduledClass::create($validated);
        return response()->json($scheduledClass, 201);
    }

    public function update(Request $request, $id)
    {
        $scheduledClass = ScheduledClass::find($id);
        if (!$scheduledClass) {
            return response()->json(['message' => 'Resource not found.'], 404);
        }
        $validated = $request->validate([
            'class_type_id' => 'exists:class_types,id',
            'instructor_id' => 'exists:users,id',
            'scheduled_at' => 'date',
            'capacity' => 'integer|min:1',
            'status' => 'in:scheduled,completed,cancelled',
            'location' => 'string|nullable',
            'description' => 'string|nullable',
        ]);
        $scheduledClass->update($validated);
        return response()->json($scheduledClass);
    }

    public function destroy($id)
    {
        $scheduledClass = ScheduledClass::find($id);
        if (!$scheduledClass) {
            return response()->json(['message' => 'Resource not found.'], 404);
        }
        $scheduledClass->delete();
        return response()->noContent();
    }

    public function indexByClassType($id)
    {
        $classType = ClassType::find($id);
        if (!$classType) {
            return response()->json(['message' => 'Resource not found.'], 404);
        }
        return $classType->scheduledClasses()->get();
    }

    public function userClasses($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Resource not found.'], 404);
        }
        // If instructor, return classes they instruct.
        if ($user->role === 'instructor') {
            return ScheduledClass::where('instructor_id', $user->id)->get();
        }
        // For 'member'.
        return [];
    }
}
