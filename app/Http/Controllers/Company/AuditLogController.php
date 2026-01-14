<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->canViewAuditLogs()) {
            abort(403, 'Unauthorized access.');
        }

        $company = $user->company;
        
        $query = $company->auditLogs()
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('event')) {
            $query->where('event', $request->event);
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
        
        // Get unique events for filter
        $events = $company->auditLogs()
            ->distinct('event')
            ->pluck('event');

        // Get users for filter
        $users = $company->users()
            ->select('id', 'name', 'email')
            ->get();

        return view('company.audit-logs', compact('logs', 'events', 'users'));
    }

    public function show(AuditLog $auditLog)
    {
        $user = Auth::user();
        
        if (!$user->canViewAuditLogs() || $auditLog->company_id !== $user->company_id) {
            abort(403, 'Unauthorized access.');
        }

        $auditLog->load('user');

        return view('company.audit-log-detail', compact('auditLog'));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->canViewAuditLogs()) {
            abort(403, 'Unauthorized access.');
        }

        $company = $user->company;
        
        $logs = $company->auditLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // In a real application, you would generate CSV or Excel file here
        // For now, we'll just return JSON
        
        return response()->json($logs);
    }
}