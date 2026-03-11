<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->isActive()) {
            return redirect()->route('payment.create')
                ->with('error', 'Необходимо оплатить членский взнос.');
        }

        return $next($request);
    }
}
