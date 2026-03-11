<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentApplication;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::withCount(['applications', 'activeApplications',
                'applications as pending_applications_count' => function ($q) {
                    $q->where('status', 'pending');
                },
            ])
            ->latest()
            ->paginate(20);

        return view('admin.tournaments.index', compact('tournaments'));
    }

    public function create()
    {
        return view('admin.tournaments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'venue' => 'nullable|string|max:255',
            'max_teams' => 'nullable|integer|min:2|max:64',
        ], [
            'name.required' => 'Введите название турнира.',
            'start_date.required' => 'Укажите дату начала.',
            'end_date.after_or_equal' => 'Дата окончания должна быть не раньше даты начала.',
            'max_teams.min' => 'Минимум 2 команды.',
            'max_teams.max' => 'Максимум 64 команды.',
        ]);

        Tournament::create([
            ...$request->only('name', 'description', 'start_date', 'end_date', 'venue', 'max_teams'),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('admin.tournaments.index')->with('success', 'Турнир создан.');
    }

    public function show(Tournament $tournament)
    {
        $tournament->load(['applications' => function ($q) {
            $q->with(['team', 'appliedBy', 'players'])->latest();
        }]);

        return view('admin.tournaments.show', compact('tournament'));
    }

    public function edit(Tournament $tournament)
    {
        return view('admin.tournaments.edit', compact('tournament'));
    }

    public function update(Request $request, Tournament $tournament)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'venue' => 'nullable|string|max:255',
            'max_teams' => 'nullable|integer|min:2|max:64',
        ], [
            'name.required' => 'Введите название турнира.',
            'start_date.required' => 'Укажите дату начала.',
            'end_date.after_or_equal' => 'Дата окончания должна быть не раньше даты начала.',
            'max_teams.min' => 'Минимум 2 команды.',
            'max_teams.max' => 'Максимум 64 команды.',
        ]);

        $tournament->update($request->only('name', 'description', 'start_date', 'end_date', 'venue', 'max_teams'));

        return back()->with('success', 'Турнир обновлён.');
    }

    public function updateStatus(Request $request, Tournament $tournament)
    {
        $request->validate([
            'status' => 'required|in:draft,registration,closed',
        ], [
            'status.required' => 'Выберите статус.',
            'status.in' => 'Недопустимый статус.',
        ]);

        $tournament->update(['status' => $request->status]);

        $statusLabels = ['draft' => 'Черновик', 'registration' => 'Регистрация открыта', 'closed' => 'Регистрация закрыта'];

        return back()->with('success', "Статус изменён: {$statusLabels[$request->status]}.");
    }

    public function approveApplication(Tournament $tournament, TournamentApplication $application)
    {
        abort_unless($application->tournament_id === $tournament->id, 404);

        if ($tournament->isFull()) {
            return back()->with('error', 'Достигнуто максимальное количество команд.');
        }

        $application->update(['status' => 'approved']);

        return back()->with('success', "Заявка команды «{$application->team->name}» одобрена.");
    }

    public function rejectApplication(Request $request, Tournament $tournament, TournamentApplication $application)
    {
        abort_unless($application->tournament_id === $tournament->id, 404);

        $application->update([
            'status' => 'rejected',
            'rejected_reason' => $request->input('reason'),
        ]);

        return back()->with('success', "Заявка команды «{$application->team->name}» отклонена.");
    }
}
