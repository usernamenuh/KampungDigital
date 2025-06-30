<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard based on user role.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        // Redirect berdasarkan role pengguna
        switch ($role) {
            case 'admin':
                return view('dashboards.admin');
                
            case 'kades':
                return view('dashboards.kades');
                
            case 'rw':
                return view('dashboards.rw');
                
            case 'rt':
                return view('dashboards.rt');
                
            case 'masyarakat':
                return view('dashboards.masyarakat');
                
            default:
                // Jika role tidak dikenali, redirect ke dashboard default
                return view('home');
        }
    }

    /**
     * Redirect user to appropriate dashboard after login
     */
    public function redirectToDashboard()
    {
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
                return redirect()->route('home');
        }
    }
}
