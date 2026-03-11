<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam();
        $stats = [
            'goals' => 0,
            'assists' => 0,
            'yellow_cards' => 0,
            'red_cards' => 0,
        ];

        return view('dashboard.index', compact('user', 'team', 'stats'));
    }
}
