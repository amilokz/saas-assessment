<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function index()
    {
        $company = Auth::user()->company;
        
        $files = $company->files()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Check storage limits
        $totalStorage = $company->files()->sum('size');
        $storageLimit = $this->getStorageLimit($company);
        $storageUsage = $storageLimit > 0 ? ($totalStorage / ($storageLimit * 1024 * 1024)) * 100 : 0;

        return view('company.files', compact('files', 'totalStorage', 'storageLimit', 'storageUsage'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        // Check permissions
        if (!$user->canUploadFiles()) {
            return redirect()->back()
                ->with('error', 'You do not have permission to upload files.');
        }

        // Check storage limits
        $totalStorage = $company->files()->sum('size');
        $storageLimit = $this->getStorageLimit($company);
        
        if ($storageLimit > 0) {
            $request->validate([
                'file' => 'required|file|max:' . ($storageLimit * 1024 - $totalStorage),
            ]);
        } else {
            $request->validate([
                'file' => 'required|file',
            ]);
        }

        $file = $request->file('file');
        
        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(20) . '.' . $extension;
        
        // Store file
        $path = $file->storeAs(
            "companies/{$company->id}/files",
            $filename,
            'local'
        );

        // Create file record
        $fileRecord = File::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'name' => $filename,
            'original_name' => $originalName,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'type' => $this->getFileType($extension),
            'is_public' => $request->boolean('is_public', false),
        ]);

        // Log file upload
        AuditLogService::logFileUpload($fileRecord);

        return redirect()->route('company.files')
            ->with('success', 'File uploaded successfully.');
    }

    public function show(File $file)
    {
        // Verify ownership
        if ($file->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized access.');
        }

        // Check if file exists
        if (!Storage::exists($file->path)) {
            abort(404, 'File not found.');
        }

        // Increment download count
        $file->incrementDownloadCount();

        // Return file download
        return Storage::download($file->path, $file->original_name);
    }

    public function destroy(File $file)
    {
        $user = Auth::user();

        // Verify ownership and permissions
        if ($file->company_id !== $user->company_id || 
            !$user->canDeleteFiles()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete file from storage
        if (Storage::exists($file->path)) {
            Storage::delete($file->path);
        }

        // Log file deletion
        AuditLogService::logFileDeleted($file);

        // Delete record
        $file->delete();

        return redirect()->route('company.files')
            ->with('success', 'File deleted successfully.');
    }

    public function toggleVisibility(File $file)
    {
        // Verify ownership
        if ($file->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        $file->update([
            'is_public' => !$file->is_public,
        ]);

        $status = $file->is_public ? 'public' : 'private';
        
        return redirect()->back()
            ->with('success', "File visibility changed to {$status}.");
    }

    private function getStorageLimit($company)
    {
        if ($company->isOnTrial()) {
            return 100; // 100MB for trial
        }

        if ($company->hasActiveSubscription() && $company->activeSubscription->plan->max_storage_mb) {
            return $company->activeSubscription->plan->max_storage_mb;
        }

        return 1024; // Default 1GB
    }

    private function getFileType($extension)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'];
        $audioExtensions = ['mp3', 'wav', 'ogg', 'flac', 'aac'];
        $archiveExtensions = ['zip', 'rar', '7z', 'tar', 'gz'];
        
        if (in_array($extension, $imageExtensions)) return 'image';
        if (in_array($extension, $videoExtensions)) return 'video';
        if (in_array($extension, $audioExtensions)) return 'audio';
        if (in_array($extension, $archiveExtensions)) return 'archive';
        
        return 'document';
    }
}