<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountNotSuspended
{
    /**
     * Cierra sesión si la cuenta está suspendida (invalidación inmediata).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->isAccountSuspended()) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->guest(route('login'))
                ->withErrors([
                    'email' => __('auth.account_suspended'),
                ]);
        }

        return $next($request);
    }
}
