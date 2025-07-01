<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Cek status user
            if ($user->status === 'inactive') {
                Log::info('Inactive user logged out automatically', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'ip' => $request->ip()
                ]);

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.');
            }

            // Cek relasi dengan penduduk untuk user masyarakat
            if ($user->role === 'masyarakat') {
                if (!$user->penduduk) {
                    Log::info('Masyarakat user without penduduk data logged out automatically', [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'ip' => $request->ip()
                    ]);

                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->with('error', 'Data kependudukan Anda tidak ditemukan. Silakan hubungi administrator.');
                }

                if ($user->penduduk->status === 'inactive') {
                    Log::info('User with inactive penduduk logged out automatically', [
                        'user_id' => $user->id,
                        'user_email' => $user->email,
                        'penduduk_id' => $user->penduduk->id,
                        'penduduk_nik' => $user->penduduk->nik,
                        'ip' => $request->ip()
                    ]);

                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->with('error', 'Data kependudukan Anda telah dinonaktifkan. Silakan hubungi administrator.');
                }
            }
        }

        return $next($request);
    }
}
