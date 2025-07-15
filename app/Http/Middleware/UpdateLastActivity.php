<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            try {
                // Update last activity timestamp
                DB::table('users')
                    ->where('id', Auth::id())
                    ->update([
                        'last_activity' => now(),
                        'is_online' => true
                    ]);
            } catch (\Exception $e) {
                // Log error tapi jangan break request
                Log::warning('Gagal update last activity', [
                    'user_id' => Auth::id(),
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $next($request);
    }
}
