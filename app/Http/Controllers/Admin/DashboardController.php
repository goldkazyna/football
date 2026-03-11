<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'verified' => User::where('verification_status', 'approved')->count(),
            'pending_verification' => User::where('verification_status', 'pending')->count(),
            'active_subscriptions' => User::where('subscription_status', 'active')->count(),
            'teams' => Team::count(),
            'total_payments' => Payment::confirmed()->sum('amount'),
        ];

        $pendingVerifications = User::where('verification_status', 'pending')
            ->whereNotNull('verification_document')
            ->latest()
            ->take(5)
            ->get();

        $pendingApplications = TeamMember::where('status', 'pending')
            ->with(['user', 'team'])
            ->latest()
            ->take(5)
            ->get();

        $recentPayments = Payment::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingVerifications', 'pendingApplications', 'recentPayments'));
    }
}
