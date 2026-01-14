<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;
        $user = Auth::user();
        
        return view('company.dashboard', [
            'company' => $company,
            'user' => $user,
        ]);
    }
}