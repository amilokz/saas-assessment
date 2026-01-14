<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_companies' => Company::count(),
            'pending_companies' => Company::where('status', 'pending')->count(),
            'active_companies' => Company::where('status', 'approved')->count(),
            'total_users' => User::count(),
        ];

        $recentCompanies = Company::latest()->take(5)->get();

        return view('super-admin.dashboard', [
            'stats' => $stats,
            'recentCompanies' => $recentCompanies,
        ]);
    }
}