<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        switch (Auth::user()->role) {
            case 'instructor':
                return redirect()->route('instructor.dashboard');

            case 'member':
                return redirect()->route('member.dashboard');

            case 'admin':
                return redirect()->route('admin.dashboard');

            default:
                return redirect()->route('login');
        }
    }
}
