<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class RoleHelper 
{
    /**
     * Get dashboard route based on user role
     */
    public static function getDashboardRoute($role = null)
    {
        if (!$role && Auth::check()) {
            $role = Auth::user()->role;
        }

        switch ($role) {
            case 'admin':
                return route('admin.dashboard');
            case 'kades':
                return route('kades.dashboard');
            case 'rw':
                return route('rw.dashboard');
            case 'rt':
                return route('rt.dashboard');
            case 'masyarakat':
                return route('masyarakat.dashboard');
            default:
                return route('home');
        }
    }

    /**
     * Check if user has access to specific roles
     */
    public static function hasRole($roles)
    {
        if (!Auth::check()) {
            return false;
        }

        $userRole = Auth::user()->role;
        
        if (is_string($roles)) {
            return $userRole === $roles;
        }

        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }

        return false;
    }

    /**
     * Get role display name in Indonesian
     */
    public static function getRoleDisplayName($role)
    {
        $roleNames = [
            'admin' => 'Administrator',
            'kades' => 'Kepala Desa',
            'rw' => 'Ketua RW',
            'rt' => 'Ketua RT',
            'masyarakat' => 'Masyarakat'
        ];

        return $roleNames[$role] ?? 'Tidak Diketahui';
    }

    /**
     * Get available menu items based on user role
     */
    public static function getMenuItems($role = null)
    {
        if (!$role && Auth::check()) {
            $role = Auth::user()->role;
        }

        $allMenus = [
            'dashboard' => ['Dashboard', 'layout-dashboard', '/home'],
            'penduduk' => ['Penduduk', 'users', '/penduduk'],
            'lokasi' => ['Lokasi', 'map-pin', '/lokasi'],
            'desa' => ['Desa', 'building-2', '/desas'], // Hanya admin
            'rt-rw' => ['RT & RW', 'home', '/rt-rw'],
            'umkm' => ['UMKM', 'store', '/umkm'],
            'wisata' => ['Wisata', 'camera', '/wisata'],
            'berita' => ['Berita', 'newspaper', '/berita'],
            'program' => ['Program', 'calendar', '/program'],
            'pembangunan' => ['Pembangunan', 'hammer', '/pembangunan'],
            'keuangan' => ['Keuangan', 'banknote', '/keuangan'],
        ];

        switch ($role) {
            case 'admin':
                return $allMenus; // Admin bisa akses semua
                
            case 'kades':
                unset($allMenus['desa']); // Kades tidak bisa akses menu desa
                return $allMenus;
                
            case 'rw':
                return [
                    'dashboard' => $allMenus['dashboard'],
                    'penduduk' => $allMenus['penduduk'],
                    'lokasi' => $allMenus['lokasi'],
                    'rt-rw' => $allMenus['rt-rw'],
                    'umkm' => $allMenus['umkm'],
                ];
                
            case 'rt':
                return [
                    'dashboard' => $allMenus['dashboard'],
                    'penduduk' => $allMenus['penduduk'],
                    'lokasi' => $allMenus['lokasi'],
                    'rt-rw' => $allMenus['rt-rw'],
                    'umkm' => $allMenus['umkm'],
                ];
                
            case 'masyarakat':
                return [
                    'dashboard' => $allMenus['dashboard'],
                ];
                
            default:
                return [
                    'dashboard' => $allMenus['dashboard'],
                ];
        }
    }
}
