<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return view('dashboard.admin');
        } elseif ($user->isInstructor()) {
            return view('dashboard.instructor');
        } else { // Default ke participant
            return view('dashboard.participant');
        }
    }
}