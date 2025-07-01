<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassType;
use Illuminate\Http\Request;

class ClassTypeController extends Controller
{
    public function index()
    {
        return ClassType::all();
    }

    public function show($id)
    {
        $classType = ClassType::find($id);
        if (!$classType) {
            return response()->json(['message' => 'Resource not found.'], 404);
        }
        return $classType;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'duration' => 'required|integer|min:1',
            'capacity' => 'integer|min:1',
            'level' => 'in:beginner,intermediate,advanced,all',
            'status' => 'in:active,inactive',
            'color' => 'string|nullable',
            'image' => 'string|nullable',
        ]);
        $classType = ClassType::create($validated);
        return response()->json($classType, 201);
    }

    public function update(Request $request, $id)
    {
        $classType = ClassType::find($id);
        if (!$classType) {
            return response()->json(['message' => 'Resource not found.'], 404);
        }
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'duration' => 'integer|min:1',
            'capacity' => 'integer|min:1',
            'level' => 'in:beginner,intermediate,advanced,all',
            'status' => 'in:active,inactive',
            'color' => 'string|nullable',
            'image' => 'string|nullable',
        ]);
        $classType->update($validated);
        return response()->json($classType);
    }

    public function destroy($id)
    {
        $classType = ClassType::find($id);
        if (!$classType) {
            return response()->json(['message' => 'Resource not found.'], 404);
        }
        $classType->delete();
        return response()->noContent();
    }
}
