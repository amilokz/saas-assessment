<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get minimal company data
        $company = $user->company;
        
        if (!$company) {
            abort(403, 'No company associated with your account.');
        }

        return view('company.dashboard', [
            'user' => $user,
            'company' => $company,
        ]);
    }
}