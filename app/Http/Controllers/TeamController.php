<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $query = Team::withCount(['members' => function ($q) {
            $q->where('status', 'approved')->whereNull('left_at');
        }])->with('captain');

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $teams = $query->latest()->paginate(20);
        $cities = Team::distinct()->pluck('city')->sort();

        return view('teams.index', compact('teams', 'cities'));
    }

    public function show(Team $team)
    {
        $team->load(['captain', 'members' => function ($q) {
            $q->where('status', 'approved')->whereNull('left_at')->with('user');
        }]);

        return view('teams.show', compact('team'));
    }

    public function create()
    {
        return view('teams.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:2048',
        ], [
            'name.required' => 'Введите название команды.',
            'city.required' => 'Укажите город.',
        ]);

        $user = $request->user();

        $team = Team::create([
            'name' => $request->name,
            'city' => $request->city,
            'description' => $request->description,
            'captain_id' => $user->id,
            'logo' => $request->hasFile('logo')
                ? $request->file('logo')->store('team_logos', 'public')
                : null,
        ]);

        $user->update(['role' => 'captain']);

        TeamMember::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'status' => 'approved',
            'joined_at' => now(),
        ]);

        return redirect()->route('my-team')->with('success', 'Команда создана!');
    }

    public function edit(Team $team)
    {
        $this->authorizeCaptain($team);
        return view('teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $this->authorizeCaptain($team);

        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:2048',
        ], [
            'name.required' => 'Введите название команды.',
            'city.required' => 'Укажите город.',
            'logo.image' => 'Логотип должен быть изображением.',
            'logo.max' => 'Логотип не должен превышать 2 МБ.',
        ]);

        $team->name = $request->name;
        $team->city = $request->city;
        $team->description = $request->description;

        if ($request->hasFile('logo')) {
            $team->logo = $request->file('logo')->store('team_logos', 'public');
        }

        $team->save();

        return redirect()->route('my-team')->with('success', 'Команда обновлена.');
    }

    private function authorizeCaptain(Team $team): void
    {
        if ($team->captain_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Только капитан может редактировать команду.');
        }
    }
}
