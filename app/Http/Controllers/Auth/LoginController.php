<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Whitelist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function loginByPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ], [
            'phone.required' => 'Введите номер телефона.',
        ]);

        $phone = preg_replace('/[^0-9+]/', '', $request->phone);

        $user = User::where('phone', $phone)->first();

        if (!$user) {
            if (!Whitelist::isWhitelisted($phone)) {
                return back()->with('error', 'Ваш номер не найден в белом списке. Обратитесь к администратору.');
            }

            return redirect()->route('register.step', 'phone')
                ->with('register_phone', $phone);
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
