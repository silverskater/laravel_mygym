<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function show(Request $request)
    {
        return $request->user();
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => "email|unique:users,email,{$user->id}",
            'phone' => 'string|nullable',
        ]);
        $user->update($validated);

        return response()->json($user);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);
        if (! \Illuminate\Support\Facades\Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }
        $user->password = \Illuminate\Support\Facades\Hash::make($validated['new_password']);
        $user->save();

        return response()->json(['message' => 'Password updated successfully.']);
    }
}
