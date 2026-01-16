<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\Subscription;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_companies' => Company::count(),
            'pending_companies' => Company::where('status', 'pending')->count(),
            'trial_companies' => Company::where('status', 'trial_pending_approval')->count(),
            'active_companies' => Company::where('status', 'approved')->count(),
            'total_users' => User::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
        ];

        $recentCompanies = Company::latest()->take(5)->get();

        return view('super-admin.dashboard', compact('stats', 'recentCompanies'));
    }
}