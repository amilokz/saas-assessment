<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    // REMOVED: Entire constructor - don't use $this->middleware()
    
    public function index()
    {
        $user = Auth::user();
        
        // Check if user is super admin
        if ($user->isSuperAdmin()) {
            abort(403, 'Super admin cannot access company files through this interface.');
        }
        
        // Get user's company
        $company = $user->company;
        
        if (!$company) {
            abort(403, 'No company associated with your account.');
        }

        // Get files for this company
        $files = File::where('company_id', $user->company_id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);

        // Calculate storage
        $totalStorage = File::where('company_id', $user->company_id)->sum('size');
        $totalStorageMB = round($totalStorage / (1024 * 1024), 2);
        
        // Trial companies: 100MB limit
        $storageLimit = $company->isOnTrial() ? 100 : 1024;
        $storageUsage = $storageLimit > 0 ? round(($totalStorageMB / $storageLimit) * 100, 1) : 0;

        // Check if can upload
        $fileCount = File::where('company_id', $user->company_id)->count();
        $canUpload = ($user->isCompanyAdmin() || $user->isSupportUser()) && 
                    (!$company->isOnTrial() || $fileCount < 2);

        return view('company.files', compact(
            'files', 
            'totalStorageMB', 
            'storageLimit', 
            'storageUsage',
            'canUpload'
        ));
    }

    public function show(File $file)
    {
        $user = Auth::user();
        
        // Verify ownership
        if ($file->company_id !== $user->company_id) {
            abort(403, 'Unauthorized access to this file.');
        }

        // In a real app, return file download
        // For now, just redirect back
        return redirect()->back()
            ->with('info', 'File download feature requires storage implementation.');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check permissions
        if (!($user->isCompanyAdmin() || $user->isSupportUser())) {
            abort(403, 'You do not have permission to upload files.');
        }
        
        $company = $user->company;

        // Check trial limitations
        if ($company->isOnTrial()) {
            $fileCount = File::where('company_id', $user->company_id)->count();
            if ($fileCount >= 2) {
                return redirect()->back()
                    ->with('error', 'Trial companies can only upload 2 files.');
            }
        }

        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        // In a real app, handle file upload here
        return redirect()->back()
            ->with('success', 'File upload feature requires storage implementation.');
    }

    public function destroy(File $file)
    {
        $user = Auth::user();

        // Verify ownership and permissions
        if ($file->company_id !== $user->company_id) {
            abort(403, 'Unauthorized action.');
        }

        if (!($user->isCompanyAdmin() || $user->isSupportUser())) {
            abort(403, 'You do not have permission to delete files.');
        }

        // In a real app, delete file from storage
        // For now, just delete from database
        $file->delete();

        return redirect()->route('company.files')
            ->with('success', 'File deleted successfully.');
    }
}