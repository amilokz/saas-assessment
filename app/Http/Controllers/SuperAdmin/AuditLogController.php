<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Company;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with(['company', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20);
        $companies = Company::orderBy('name')->get();
        $users = \App\Models\User::whereIn('role_id', [2,3,4])->get(); // Company users only

        return view('super-admin.audit-logs', compact('logs', 'companies', 'users'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load(['company', 'user']);
        
        return view('super-admin.audit-logs.show', compact('auditLog'));
    }
}