<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckNotBlocked
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->is_blocked) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Ваш аккаунт заблокирован. Обратитесь к администратору.');
        }

        return $next($request);
    }
}
