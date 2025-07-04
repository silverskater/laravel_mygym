<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if (in_array(Auth::user()->role, User::ROLES)) {
            $route = Auth::user()->role.'.dashboard';
        } else {
            $route = 'login';
        }

        return redirect()->route($route);
    }
}
