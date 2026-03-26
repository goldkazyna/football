<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Whitelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function checkIin(Request $request)
    {
        $request->validate([
            'iin' => 'required|string|digits:12',
        ], [
            'iin.required' => 'Введите ИИН.',
            'iin.digits' => 'ИИН должен содержать 12 цифр.',
        ]);

        $iin = $request->iin;

        $user = User::where('iin', $iin)->where('del', false)->first();

        if ($user) {
            return response()->json(['status' => $user->password ? 'password' : 'login']);
        }

        if (Whitelist::isWhitelisted($iin)) {
            return response()->json(['status' => 'register']);
        }

        return response()->json(['status' => 'not_found']);
    }

    public function loginByIin(Request $request)
    {
        $request->validate([
            'iin' => 'required|string|digits:12',
            'password' => 'nullable|string',
        ], [
            'iin.required' => 'Введите ИИН.',
            'iin.digits' => 'ИИН должен содержать 12 цифр.',
        ]);

        $iin = $request->iin;

        $user = User::where('iin', $iin)->where('del', false)->first();

        if (!$user) {
            if (!Whitelist::isWhitelisted($iin)) {
                return back()->with('error', 'Ваш ИИН не найден в белом списке. Обратитесь к администратору.');
            }

            session(['registration' => ['iin' => $iin]]);
            return redirect()->route('register.step', 'password');
        }

        // User exists — check password (skip for users without password, e.g. superadmin)
        if ($user->password) {
            if (!$request->password) {
                return back()->withInput()->withErrors(['password' => 'Введите пароль.']);
            }

            if (!Hash::check($request->password, $user->password)) {
                return back()->withInput()->withErrors(['password' => 'Неверный пароль.']);
            }
        }

        Auth::login($user, true);

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
