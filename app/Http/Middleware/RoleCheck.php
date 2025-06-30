<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            Log::warning('Unauthorized access attempt - User not authenticated', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->userAgent()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil role user yang sedang login
        $user = Auth::user();
        $userRole = $user->role;

        // Cek apakah role user termasuk dalam role yang diizinkan
        if (!in_array($userRole, $roles)) {
            Log::warning('Access denied - Insufficient role permissions', [
                'user_id' => $user->id,
                'user_role' => $userRole,
                'required_roles' => $roles,
                'url' => $request->fullUrl(),
                'ip' => $request->ip()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden - Anda tidak memiliki akses ke halaman ini.',
                    'required_roles' => $roles,
                    'your_role' => $userRole
                ], 403);
            }

            // Redirect ke dashboard sesuai role user
            $redirectRoute = $this->getDashboardRoute($userRole);
            
            return redirect()->route($redirectRoute)
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }

    /**
     * Get dashboard route based on user role
     */
    private function getDashboardRoute($role)
    {
        switch ($role) {
            case 'admin':
                return 'admin.dashboard';
            case 'kades':
                return 'kades.dashboard';
            case 'rw':
                return 'rw.dashboard';
            case 'rt':
                return 'rt.dashboard';
            case 'masyarakat':
                return 'masyarakat.dashboard';
            default:
                return 'home';
        }
    }
}
