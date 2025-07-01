<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Resource not found.'], 404);
        }
        return $user;
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Resource not found.'], 404);
        }
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => "email|unique:users,email,{$user->id}",
            'role' => 'in:member,instructor,admin',
            'phone' => 'string|nullable',
        ]);
        $user->update($validated);
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Resource not found.'], 404);
        }
        $user->delete();
        return response()->noContent();
    }
}
