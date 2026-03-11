<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Суперадмин всегда проходит
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if ($user->verification_status !== 'approved') {
            return redirect()->route('dashboard')
                ->with('error', 'Дождитесь подтверждения верификации для доступа к этому разделу.');
        }

        return $next($request);
    }
}
