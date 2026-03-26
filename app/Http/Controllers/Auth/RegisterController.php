<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Whitelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    private array $steps = ['iin', 'password', 'profile', 'team', 'verification', 'payment'];

    public function showStep(string $step)
    {
        if (!in_array($step, $this->steps)) {
            abort(404);
        }

        $data = session('registration', []);

        if ($step === 'team') {
            $iin = $data['iin'] ?? null;
            $assignedTeam = null;

            if ($iin) {
                // Check if admin pre-created a team for this captain
                $assignedTeam = Team::where('captain_iin', $iin)->first();

                // Or if added by a captain — player goes to captain's team
                if (!$assignedTeam) {
                    $whitelistEntry = Whitelist::where('iin', $iin)->first();
                    if ($whitelistEntry && $whitelistEntry->added_by) {
                        $addedBy = User::find($whitelistEntry->added_by);
                        if ($addedBy && $addedBy->role === 'captain') {
                            $assignedTeam = Team::where('captain_id', $addedBy->id)->first();
                        }
                    }
                }
            }

            return view("register.step-{$step}", compact('data', 'assignedTeam'));
        }

        return view("register.step-{$step}", compact('data'));
    }

    public function processStep(Request $request, string $step)
    {
        if (!in_array($step, $this->steps)) {
            abort(404);
        }

        $data = session('registration', []);

        switch ($step) {
            case 'iin':
                return $this->processIin($request, $data);
            case 'password':
                return $this->processPassword($request, $data);
            case 'profile':
                return $this->processProfile($request, $data);
            case 'team':
                return $this->processTeam($request, $data);
            case 'verification':
                return $this->processVerification($request, $data);
            case 'payment':
                return $this->processPayment($request, $data);
        }

        return redirect()->route('register.step', $step);
    }

    private function processIin(Request $request, array $data)
    {
        $request->validate([
            'iin' => 'required|string|digits:12',
        ], [
            'iin.required' => 'Введите ИИН.',
            'iin.digits' => 'ИИН должен содержать 12 цифр.',
        ]);

        $iin = $request->iin;

        if (!Whitelist::isWhitelisted($iin)) {
            return back()->with('error', 'Ваш ИИН не найден в белом списке. Обратитесь к администратору.');
        }

        if (User::where('iin', $iin)->where('del', false)->exists()) {
            return redirect()->route('login')->with('error', 'Этот ИИН уже зарегистрирован. Войдите в систему.');
        }

        $data['iin'] = $iin;
        session(['registration' => $data]);

        return redirect()->route('register.step', 'password');
    }

    private function processPassword(Request $request, array $data)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.required' => 'Введите пароль.',
            'password.min' => 'Пароль должен содержать минимум 6 символов.',
            'password.confirmed' => 'Пароли не совпадают.',
        ]);

        $data['password'] = $request->password;
        session(['registration' => $data]);

        return redirect()->route('register.step', 'profile');
    }

    private function processProfile(Request $request, array $data)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
        ], [
            'name.required' => 'Введите ваше имя.',
            'city.required' => 'Укажите город.',
            'specialization.required' => 'Укажите специализацию.',
        ]);

        $data['name'] = $request->name;
        $data['city'] = $request->city;
        $data['specialization'] = $request->specialization;
        session(['registration' => $data]);

        return redirect()->route('register.step', 'team');
    }

    private function processTeam(Request $request, array $data)
    {
        $mode = $request->input('team_mode', 'skip');

        if ($mode === 'assigned') {
            $data['team_action'] = 'join';
            $data['team_id'] = $request->input('team_id');
        } elseif ($mode === 'create') {
            $request->validate([
                'team_name' => 'required|string|max:255',
                'team_city' => 'required|string|max:255',
            ], [
                'team_name.required' => 'Введите название команды.',
                'team_city.required' => 'Укажите город команды.',
            ]);

            $data['team_action'] = 'create';
            $data['team_name'] = $request->team_name;
            $data['team_city'] = $request->team_city;
            $data['team_description'] = $request->team_description;
        } else {
            $data['team_action'] = 'skip';
        }

        $data['team_mode'] = $mode;
        $data['team_id'] = $request->team_id;
        session(['registration' => $data]);

        return redirect()->route('register.step', 'verification');
    }

    private function processVerification(Request $request, array $data)
    {
        $request->validate([
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ], [
            'document.required' => 'Загрузите документ для верификации.',
            'document.mimes' => 'Допустимые форматы: JPG, PNG, PDF.',
            'document.max' => 'Максимальный размер файла — 10 МБ.',
            'document.uploaded' => 'Файл слишком большой. Максимум 10 МБ.',
        ]);

        $path = $request->file('document')->store('verification_documents', 'public');
        $data['verification_document'] = $path;
        session(['registration' => $data]);

        return redirect()->route('register.step', 'payment');
    }

    private function processPayment(Request $request, array $data)
    {
        if (empty($data['iin']) || empty($data['name'])) {
            return redirect()->route('register.step', 'iin')
                ->with('error', 'Пожалуйста, начните регистрацию сначала.');
        }

        // Determine role from whitelist
        $whitelistEntry = Whitelist::where('iin', $data['iin'])->first();
        $role = $whitelistEntry->role ?? 'player';

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'iin' => $data['iin'],
            'password' => $data['password'] ?? null,
            'city' => $data['city'] ?? null,
            'specialization' => $data['specialization'] ?? null,
            'verification_status' => 'pending',
            'verification_document' => $data['verification_document'] ?? null,
            'role' => $role,
        ]);

        // Handle team
        // 1. Check if admin already created a team for this captain (by iin)
        $preAssignedTeam = Team::where('captain_iin', $data['iin'])->whereNull('captain_id')->first();

        if ($preAssignedTeam) {
            // Admin pre-created team — assign captain
            $preAssignedTeam->update(['captain_id' => $user->id]);
            $user->update(['role' => 'captain']);

            TeamMember::create([
                'team_id' => $preAssignedTeam->id,
                'user_id' => $user->id,
                'status' => 'approved',
                'joined_at' => now(),
            ]);
        } elseif (($data['team_action'] ?? '') === 'create') {
            $team = Team::create([
                'name' => $data['team_name'],
                'city' => $data['team_city'] ?? $data['city'],
                'description' => $data['team_description'] ?? null,
                'captain_id' => $user->id,
                'captain_iin' => $user->iin,
            ]);

            $user->update(['role' => 'captain']);

            TeamMember::create([
                'team_id' => $team->id,
                'user_id' => $user->id,
                'status' => 'approved',
                'joined_at' => now(),
            ]);
        } elseif (!empty($data['team_id'])) {
            TeamMember::create([
                'team_id' => $data['team_id'],
                'user_id' => $user->id,
                'status' => 'approved',
                'joined_at' => now(),
            ]);
        }

        session()->forget('registration');

        Auth::login($user, true);

        return redirect()->route('register.pending');
    }

    public function pending()
    {
        return view('register.pending');
    }
}
