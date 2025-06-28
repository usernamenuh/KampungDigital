<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        // Ambil role user yang sedang login
        $userRole = Auth::user()->role;

        // Cek apakah role user termasuk dalam role yang diizinkan
        if (!in_array($userRole, haystack: $roles)) {
            abort(403, message: 'Unauthorized');
        }

        return $next($request);
    }
}
