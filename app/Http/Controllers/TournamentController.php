<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentApplication;
use App\Models\User;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $team = $user->currentTeam();

        $tournaments = Tournament::visible()
            ->withCount('activeApplications')
            ->latest()
            ->paginate(20);

        $teamApplications = [];
        if ($team) {
            $teamApplications = TournamentApplication::where('team_id', $team->id)
                ->pluck('status', 'tournament_id')
                ->toArray();
        }

        return view('tournaments.index', compact('tournaments', 'teamApplications', 'team'));
    }

    public function show(Tournament $tournament)
    {
        abort_if($tournament->status === 'draft', 404);

        $tournament->load(['approvedApplications.team']);

        $user = auth()->user();
        $team = $user->currentTeam();
        $application = null;

        if ($team) {
            $application = TournamentApplication::where('tournament_id', $tournament->id)
                ->where('team_id', $team->id)
                ->first();
        }

        return view('tournaments.show', compact('tournament', 'team', 'application'));
    }

    public function apply(Tournament $tournament)
    {
        $user = auth()->user();

        abort_unless($user->isCaptain() || $user->isSuperAdmin(), 403, 'Только капитан может подать заявку.');
        abort_unless($tournament->isOpenForRegistration(), 403, 'Регистрация закрыта.');

        $team = $user->currentTeam();
        abort_unless($team, 403, 'У вас нет команды.');

        $existing = TournamentApplication::where('tournament_id', $tournament->id)
            ->where('team_id', $team->id)
            ->first();

        if ($existing && $existing->status !== 'rejected') {
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Ваша команда уже подала заявку на этот турнир.');
        }

        $team->load(['members' => function ($q) {
            $q->where('status', 'approved')
              ->whereNull('left_at')
              ->with('user');
        }]);

        $members = $team->members->map(fn($m) => $m->user)->filter();

        return view('tournaments.apply', compact('tournament', 'team', 'members'));
    }

    public function submitApplication(Request $request, Tournament $tournament)
    {
        $user = auth()->user();

        abort_unless($user->isCaptain() || $user->isSuperAdmin(), 403);
        abort_unless($tournament->isOpenForRegistration(), 403, 'Регистрация закрыта.');

        $team = $user->currentTeam();
        abort_unless($team, 403);

        $existing = TournamentApplication::where('tournament_id', $tournament->id)
            ->where('team_id', $team->id)
            ->first();

        if ($existing && $existing->status === 'rejected') {
            $existing->delete();
        } elseif ($existing) {
            return back()->with('error', 'Заявка уже подана.');
        }

        if ($tournament->isFull()) {
            return back()->with('error', 'Турнир уже заполнен.');
        }

        $request->validate([
            'players' => 'required|array|min:1',
            'players.*' => 'exists:users,id',
        ], [
            'players.required' => 'Выберите хотя бы одного игрока.',
            'players.min' => 'Выберите хотя бы одного игрока.',
        ]);

        $playerIds = $request->input('players');
        $activePlayers = User::whereIn('id', $playerIds)
            ->where('subscription_status', 'active')
            ->where('subscription_expires_at', '>', now())
            ->pluck('id')
            ->toArray();

        if (count($activePlayers) !== count($playerIds)) {
            return back()->with('error', 'Некоторые выбранные игроки не имеют активной подписки.');
        }

        $application = TournamentApplication::create([
            'tournament_id' => $tournament->id,
            'team_id' => $team->id,
            'applied_by' => $user->id,
        ]);

        $application->players()->attach($playerIds);

        return redirect()->route('tournaments.show', $tournament)
            ->with('success', 'Заявка подана! Ожидайте одобрения.');
    }
}
