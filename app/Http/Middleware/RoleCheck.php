<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Check if user has any of the required roles
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // If user doesn't have required role, redirect or abort
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diizinkan'
            ], 403);
        }

        // Redirect ke dashboard dengan pesan error
        return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    }
}
