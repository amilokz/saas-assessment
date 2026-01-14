<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $company = Auth::user()->company;
        
        $query = $company->files()
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('original_name', 'like', '%' . $request->search . '%');
        }

        $files = $query->paginate($request->get('per_page', 20));

        // Storage stats
        $totalStorage = $company->files()->sum('size');
        $storageLimit = $this->getStorageLimit($company);
        $storageUsage = $storageLimit > 0 ? ($totalStorage / ($storageLimit * 1024 * 1024)) * 100 : 0;

        return response()->json([
            'data' => $files,
            'stats' => [
                'total_storage' => $totalStorage,
                'storage_limit' => $storageLimit,
                'storage_usage' => $storageUsage,
                'total_files' => $files->total(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        // Check permissions
        if (!$user->canUploadFiles()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check storage limits
        $totalStorage = $company->files()->sum('size');
        $storageLimit = $this->getStorageLimit($company);
        
        $maxFileSize = $storageLimit > 0 ? ($storageLimit * 1024 - $totalStorage) : 10240; // 10MB default

        $validated = $request->validate([
            'file' => 'required|file|max:' . $maxFileSize,
            'is_public' => 'boolean',
        ]);

        $file = $request->file('file');
        
        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(20) . '.' . $extension;
        
        // Store file
        $path = $file->storeAs(
            "companies/{$company->id}/files",
            $filename,
            'company_files'
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

        return response()->json([
            'message' => 'File uploaded successfully',
            'data' => $fileRecord,
        ], 201);
    }

    public function show(File $file)
    {
        // Verify ownership
        if ($file->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $file->load('user');
        
        // Increment download count
        $file->incrementDownloadCount();

        return response()->json([
            'data' => $file,
            'download_url' => route('company.files.download', $file),
        ]);
    }

    public function destroy(File $file)
    {
        $user = Auth::user();

        // Verify ownership and permissions
        if ($file->company_id !== $user->company_id || 
            !$user->canDeleteFiles()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Delete file from storage
        if (Storage::disk('company_files')->exists($file->path)) {
            Storage::disk('company_files')->delete($file->path);
        }

        // Log file deletion
        AuditLogService::logFileDeleted($file);

        // Delete record
        $file->delete();

        return response()->json([
            'message' => 'File deleted successfully',
        ]);
    }

    public function toggleVisibility(File $file)
    {
        // Verify ownership
        if ($file->company_id !== Auth::user()->company_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $file->update([
            'is_public' => !$file->is_public,
        ]);

        return response()->json([
            'message' => 'File visibility updated',
            'data' => $file,
        ]);
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