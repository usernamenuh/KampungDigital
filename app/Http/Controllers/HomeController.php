<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Penduduk;
use App\Models\Kas;
use App\Models\Notifikasi;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Kk;
use Carbon\Carbon;

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
     * Redirect to appropriate dashboard based on user role
     */
    public function redirectToDashboard()
    {
        $user = Auth::user();
        
        switch ($user->role) {
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
                return $this->index();
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        
        // Data dasar untuk semua role
        $data = [
            'user' => $user,
            'notifications' => $this->getNotifications($user),
            'stats' => $this->getStats($user),
        ];

        // Data spesifik berdasarkan role
        switch ($user->role) {
            case 'admin':
            case 'kades':
                $data = array_merge($data, $this->getAdminData($user));
                break;
            case 'rw':
                $data = array_merge($data, $this->getRwData($user));
                break;
            case 'rt':
                $data = array_merge($data, $this->getRtData($user));
                break;
            case 'masyarakat':
                $data = array_merge($data, $this->getMasyarakatData($user));
                break;
        }

        return view('dashboards.' . $user->role, $data);
    }

    /**
     * Get notifications for user
     */
    private function getNotifications($user)
    {
        return Notifikasi::where('user_id', $user->id)
            ->where('dibaca', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get basic stats for user
     */
    private function getStats($user)
    {
        $stats = [
            'total_users' => 0,
            'total_penduduk' => 0,
            'total_kas_bulan_ini' => 0,
            'total_kas_terkumpul' => 0,
            'kas_belum_bayar' => 0,
            'kas_terlambat' => 0,
        ];

        try {
            switch ($user->role) {
                case 'admin':
                case 'kades':
                    $stats['total_users'] = User::count();
                    $stats['total_penduduk'] = Penduduk::where('status', 'aktif')->count();
                    $stats['total_kas_bulan_ini'] = Kas::whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->sum('jumlah');
                    $stats['total_kas_terkumpul'] = Kas::where('status', 'lunas')->sum('jumlah');
                    $stats['kas_belum_bayar'] = Kas::where('status', 'belum_bayar')->count();
                    $stats['kas_terlambat'] = Kas::where('status', 'terlambat')->count();
                    break;

                case 'rw':
                    if ($user->penduduk && $user->penduduk->kk && $user->penduduk->kk->rt) {
                        $rwId = $user->penduduk->kk->rt->rw_id;
                        $stats['total_penduduk'] = Penduduk::whereHas('kk.rt', function($q) use ($rwId) {
                            $q->where('rw_id', $rwId);
                        })->where('status', 'aktif')->count();
                        
                        $stats['total_kas_terkumpul'] = Kas::whereHas('rt', function($q) use ($rwId) {
                            $q->where('rw_id', $rwId);
                        })->where('status', 'lunas')->sum('jumlah');
                        
                        $stats['kas_belum_bayar'] = Kas::whereHas('rt', function($q) use ($rwId) {
                            $q->where('rw_id', $rwId);
                        })->where('status', 'belum_bayar')->count();
                    }
                    break;

                case 'rt':
                    if ($user->penduduk && $user->penduduk->kk) {
                        $rtId = $user->penduduk->kk->rt_id;
                        $stats['total_penduduk'] = Penduduk::whereHas('kk', function($q) use ($rtId) {
                            $q->where('rt_id', $rtId);
                        })->where('status', 'aktif')->count();
                        
                        $stats['total_kas_terkumpul'] = Kas::where('rt_id', $rtId)
                            ->where('status', 'lunas')->sum('jumlah');
                        
                        $stats['kas_belum_bayar'] = Kas::where('rt_id', $rtId)
                            ->where('status', 'belum_bayar')->count();
                    }
                    break;

                case 'masyarakat':
                    if ($user->penduduk) {
                        $stats['kas_belum_bayar'] = Kas::where('penduduk_id', $user->penduduk->id)
                            ->where('status', 'belum_bayar')->count();
                        
                        $stats['kas_terlambat'] = Kas::where('penduduk_id', $user->penduduk->id)
                            ->where('status', 'terlambat')->count();
                        
                        $stats['total_kas_terkumpul'] = Kas::where('penduduk_id', $user->penduduk->id)
                            ->where('status', 'lunas')->sum('jumlah');
                    }
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('Error getting stats: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get admin specific data
     */
    private function getAdminData($user)
    {
        return [
            'recent_kas' => Kas::with(['penduduk', 'rt.rw'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'kas_chart_data' => $this->getKasChartData(),
            'rt_stats' => Rt::withCount(['kas', 'kks'])->limit(10)->get(),
        ];
    }

    /**
     * Get RW specific data
     */
    private function getRwData($user)
    {
        $data = [];
        
        if ($user->penduduk && $user->penduduk->kk && $user->penduduk->kk->rt) {
            $rwId = $user->penduduk->kk->rt->rw_id;
            
            $data['recent_kas'] = Kas::with(['penduduk', 'rt'])
                ->whereHas('rt', function($q) use ($rwId) {
                    $q->where('rw_id', $rwId);
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
                
            $data['rt_list'] = Rt::where('rw_id', $rwId)
                ->withCount(['kas', 'kks'])
                ->get();
        }
        
        return $data;
    }

    /**
     * Get RT specific data
     */
    private function getRtData($user)
    {
        $data = [];
        
        if ($user->penduduk && $user->penduduk->kk) {
            $rtId = $user->penduduk->kk->rt_id;
            
            $data['recent_kas'] = Kas::with(['penduduk'])
                ->where('rt_id', $rtId)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
                
            $data['penduduk_list'] = Penduduk::whereHas('kk', function($q) use ($rtId) {
                $q->where('rt_id', $rtId);
            })->where('status', 'aktif')->limit(10)->get();
        }
        
        return $data;
    }

    /**
     * Get masyarakat specific data
     */
    private function getMasyarakatData($user)
    {
        $data = [];
        
        if ($user->penduduk) {
            $data['my_kas'] = Kas::where('penduduk_id', $user->penduduk->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
                
            $data['upcoming_kas'] = Kas::where('penduduk_id', $user->penduduk->id)
                ->where('status', 'belum_bayar')
                ->where('tanggal_jatuh_tempo', '>=', now())
                ->orderBy('tanggal_jatuh_tempo', 'asc')
                ->limit(5)
                ->get();
        }
        
        return $data;
    }

    /**
     * Get kas chart data for admin
     */
    private function getKasChartData()
    {
        try {
            $months = [];
            $kasData = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $months[] = $date->format('M Y');
                
                $total = Kas::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->where('status', 'lunas')
                    ->sum('jumlah');
                    
                $kasData[] = $total;
            }
            
            return [
                'labels' => $months,
                'data' => $kasData,
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting kas chart data: ' . $e->getMessage());
            return [
                'labels' => [],
                'data' => [],
            ];
        }
    }
}
