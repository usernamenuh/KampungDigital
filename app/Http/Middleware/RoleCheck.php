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

        // Ambil user yang sedang login
        $user = Auth::user();

        // Cek status user - jika inactive, logout dan redirect
        if ($user->status === 'inactive') {
            Log::warning('Inactive user attempted to access system', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'url' => $request->fullUrl(),
                'ip' => $request->ip()
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.'
                ], 403);
            }

            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.');
        }

        // Cek relasi dengan penduduk - jika penduduk dihapus atau nonaktif, logout
        if ($user->penduduk) {
            // Jika penduduk statusnya inactive
            if ($user->penduduk->status === 'inactive') {
                Log::warning('User with inactive penduduk attempted to access system', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'penduduk_id' => $user->penduduk->id,
                    'penduduk_nik' => $user->penduduk->nik,
                    'penduduk_status' => $user->penduduk->status,
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip()
                ]);

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Data kependudukan Anda telah dinonaktifkan. Silakan hubungi administrator.'
                    ], 403);
                }

                return redirect()->route('login')->with('error', 'Data kependudukan Anda telah dinonaktifkan. Silakan hubungi administrator.');
            }
        } else {
            // Jika user punya role masyarakat tapi tidak ada data penduduk, logout
            if ($user->role === 'masyarakat') {
                Log::warning('Masyarakat user without penduduk data attempted to access system', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_role' => $user->role,
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip()
                ]);

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Data kependudukan Anda tidak ditemukan. Silakan hubungi administrator.'
                    ], 403);
                }

                return redirect()->route('login')->with('error', 'Data kependudukan Anda tidak ditemukan. Silakan hubungi administrator.');
            }
        }

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
