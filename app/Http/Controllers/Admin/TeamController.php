<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Whitelist;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $query = Team::withCount(['members' => function ($q) {
            $q->where('status', 'approved')->whereNull('left_at');
        }])->with('captain');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $teams = $query->latest()->paginate(20);

        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        // Капитаны из whitelist (могут быть ещё не зарегистрированы)
        $whitelistCaptains = Whitelist::where('role', 'captain')->get();

        // Для каждого проверим, зарегистрирован ли уже
        $captainOptions = $whitelistCaptains->map(function ($wl) {
            $user = User::where('phone', $wl->phone)->first();
            return (object) [
                'phone' => $wl->phone,
                'name' => $user ? $user->name : null,
                'user_id' => $user?->id,
                'registered' => (bool) $user,
            ];
        });

        return view('admin.teams.create', compact('captainOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'captain_phone' => 'required|string',
        ], [
            'name.required' => 'Введите название команды.',
            'city.required' => 'Укажите город.',
            'captain_phone.required' => 'Выберите капитана.',
        ]);

        $captainPhone = $request->captain_phone;
        $captain = User::where('phone', $captainPhone)->first();

        $team = Team::create([
            'name' => $request->name,
            'city' => $request->city,
            'description' => $request->description,
            'captain_phone' => $captainPhone,
            'captain_id' => $captain?->id,
        ]);

        // Если капитан уже зарегистрирован — назначим роль и добавим в команду
        if ($captain) {
            $captain->update(['role' => 'captain']);

            TeamMember::create([
                'team_id' => $team->id,
                'user_id' => $captain->id,
                'status' => 'approved',
                'joined_at' => now(),
            ]);
        }

        return redirect()->route('admin.teams.index')->with('success', "Команда «{$team->name}» создана.");
    }

    public function show(Team $team)
    {
        $team->load(['captain', 'members' => function ($q) {
            $q->with('user')->latest();
        }]);

        return view('admin.teams.show', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ], [
            'name.required' => 'Введите название команды.',
            'city.required' => 'Укажите город.',
        ]);

        $team->update($request->only('name', 'city', 'description'));

        return back()->with('success', 'Команда обновлена.');
    }

    public function changeCaptain(Request $request, Team $team)
    {
        $request->validate([
            'captain_id' => 'required|exists:users,id',
        ], [
            'captain_id.required' => 'Выберите нового капитана.',
            'captain_id.exists' => 'Выбранный пользователь не найден.',
        ]);

        $newCaptain = User::findOrFail($request->captain_id);
        $oldCaptain = $team->captain;

        if ($oldCaptain) {
            $oldCaptain->update(['role' => 'player']);
        }

        $team->update(['captain_id' => $newCaptain->id]);
        $newCaptain->update(['role' => 'captain']);

        return back()->with('success', "Капитан команды изменён на {$newCaptain->name}.");
    }
}
