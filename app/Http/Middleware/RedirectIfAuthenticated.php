<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect berdasarkan role setelah login
                $user = Auth::user();
                $role = $user->role;

                switch ($role) {
                    case 'admin':
                        return redirect()->route('admin.dashboard');
                    case 'kades':
                        return redirect()->route('kades.dashboard');
                    case 'rw':
                        return redirect()->route('rw.dashboard');
                    case 'rt':
                        return redirect()->route('rt.dashboard');
                    case 'masyarakat':
                        return redirect()->route('masyarakat.dashboard');
                    default:
                        return redirect(RouteServiceProvider::HOME);
                }
            }
        }

        return $next($request);
    }
}
