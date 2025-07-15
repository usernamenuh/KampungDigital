<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard - Acts as a redirector only
     */
    public function index()
    {
        $user = Auth::user();
        
        // Simple redirector based on user role
        switch ($user->role) {
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
                return view('dashboards.default');
        }
    }

    /**
     * Individual dashboard methods - simplified to just return views
     */
    public function adminDashboard()
    {
        return view('dashboards.admin');
    }

    public function kadesDashboard()
    {
        return view('dashboards.kades');
    }

    public function rwDashboard()
    {
        return view('dashboards.rw');
    }

    public function rtDashboard()
    {
        return view('dashboards.rt');
    }

    public function masyarakatDashboard()
    {
        return view('dashboards.masyarakat');
    }
}
