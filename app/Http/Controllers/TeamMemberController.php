<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    public function myTeam(Request $request)
    {
        $user = $request->user();
        $team = $user->currentTeam();

        if (!$team) {
            return view('my-team.show', ['team' => null, 'members' => collect()]);
        }

        $team->load(['captain', 'members' => function ($q) {
            $q->where('status', 'approved')->whereNull('left_at')->with('user');
        }]);

        return view('my-team.show', compact('team'));
    }

    public function leaveTeam(Request $request)
    {
        $membership = $request->user()->currentTeamMembership();

        if ($membership) {
            $membership->update(['left_at' => now()]);
        }

        return redirect()->route('dashboard')->with('success', 'Вы покинули команду.');
    }

    public function applyToTeam(Request $request, Team $team)
    {
        $user = $request->user();

        $existing = TeamMember::where('user_id', $user->id)
            ->where('team_id', $team->id)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            return back()->with('error', 'Вы уже подали заявку в эту команду.');
        }

        $currentMembership = $user->currentTeamMembership();
        if ($currentMembership) {
            return back()->with('error', 'Сначала покиньте текущую команду.');
        }

        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Заявка отправлена. Ожидайте подтверждения от капитана.');
    }
}
