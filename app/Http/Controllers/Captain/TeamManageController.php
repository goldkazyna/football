<?php

namespace App\Http\Controllers\Captain;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;

class TeamManageController extends Controller
{
    public function applications(Request $request)
    {
        $team = $request->user()->captainOfTeam;

        if (!$team) {
            abort(403, 'Вы не являетесь капитаном команды.');
        }

        $applications = $team->members()
            ->where('status', 'pending')
            ->with('user')
            ->latest()
            ->get();

        $members = $team->members()
            ->where('status', 'approved')
            ->whereNull('left_at')
            ->with('user')
            ->get();

        // Игроки команды, ожидающие верификации (исключаем самого капитана)
        $memberUserIds = $members->pluck('user_id');
        $pendingVerifications = User::whereIn('id', $memberUserIds)
            ->where('id', '!=', $request->user()->id)
            ->where('verification_status', 'pending')
            ->whereNotNull('verification_document')
            ->get();

        return view('my-team.manage', compact('team', 'applications', 'members', 'pendingVerifications'));
    }

    public function approveApplication(Request $request, TeamMember $member)
    {
        $this->authorizeCaptainFor($member, $request);
        $this->requireSubscription($request);

        $member->update([
            'status' => 'approved',
            'joined_at' => now(),
        ]);

        return back()->with('success', 'Игрок принят в команду.');
    }

    public function rejectApplication(Request $request, TeamMember $member)
    {
        $this->authorizeCaptainFor($member, $request);
        $this->requireSubscription($request);

        $member->update(['status' => 'rejected']);

        return back()->with('success', 'Заявка отклонена.');
    }

    public function removeMember(Request $request, TeamMember $member)
    {
        $this->authorizeCaptainFor($member, $request);
        $this->requireSubscription($request);

        $member->update(['left_at' => now()]);

        return back()->with('success', 'Игрок удалён из команды.');
    }

    public function verifyMember(Request $request, User $user)
    {
        $this->requireSubscription($request);
        $team = $request->user()->captainOfTeam;
        abort_unless($team, 403);

        // Проверяем что юзер в команде капитана
        $isMember = $team->members()->where('user_id', $user->id)->where('status', 'approved')->exists();
        abort_unless($isMember, 403, 'Этот игрок не в вашей команде.');

        $user->update(['verification_status' => 'approved']);

        return back()->with('success', "{$user->name} верифицирован.");
    }

    public function rejectMember(Request $request, User $user)
    {
        $this->requireSubscription($request);
        $team = $request->user()->captainOfTeam;
        abort_unless($team, 403);

        $isMember = $team->members()->where('user_id', $user->id)->where('status', 'approved')->exists();
        abort_unless($isMember, 403, 'Этот игрок не в вашей команде.');

        $user->update(['verification_status' => 'rejected']);

        return back()->with('success', "Верификация {$user->name} отклонена.");
    }

    private function authorizeCaptainFor(TeamMember $member, Request $request): void
    {
        $team = $member->team;

        if ($team->captain_id !== $request->user()->id && !$request->user()->isSuperAdmin()) {
            abort(403, 'Доступ запрещён.');
        }
    }

    private function requireSubscription(Request $request): void
    {
        if ($request->user()->isSuperAdmin()) {
            return;
        }

        if (!$request->user()->isActive()) {
            abort(403, 'Для этого действия необходима активная подписка.');
        }
    }
}
