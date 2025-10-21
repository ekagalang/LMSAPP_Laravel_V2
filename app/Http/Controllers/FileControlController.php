<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\ActivityLog;

class FileControlController extends Controller
{
    /**
     * Display file control page
     */
    public function index()
    {
        $files = $this->getAllFiles();
        return view('file-control.index', compact('files'));
    }

    /**
     * Get all files with details
     */
    private function getAllFiles()
    {
        $directories = ['images/posts', 'files'];
        $allFiles = [];

        foreach ($directories as $dir) {
            if (Storage::disk('public')->exists($dir)) {
                $files = Storage::disk('public')->allFiles($dir);

                foreach ($files as $file) {
                    $allFiles[] = [
                        'name' => basename($file),
                        'path' => $file,
                        'full_path' => Storage::disk('public')->path($file),
                        'url' => Storage::url($file),
                        'size' => Storage::disk('public')->size($file),
                        'modified' => Storage::disk('public')->lastModified($file),
                        'extension' => pathinfo($file, PATHINFO_EXTENSION),
                        'type' => $this->getFileType($file),
                        'directory' => dirname($file)
                    ];
                }
            }
        }

        // Sort by modified date, newest first
        usort($allFiles, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        return $allFiles;
    }

    /**
     * Upload new file
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:20480', // 20MB
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);

            // Determine directory based on file type
            $directory = $this->isImage($extension) ? 'images/posts' : 'files';

            // Generate unique filename
            $uniqueName = $fileName . '_' . time() . '.' . $extension;

            // Store file
            $path = $file->storeAs($directory, $uniqueName, 'public');

            // Log activity
            ActivityLog::log('upload', [
                'file_name' => $originalName,
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'description' => "Uploaded file '{$originalName}' to {$directory}",
                'metadata' => [
                    'original_name' => $originalName,
                    'stored_name' => $uniqueName,
                    'directory' => $directory,
                    'extension' => $extension,
                ],
                'status' => 'success'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'file' => [
                    'name' => $originalName,
                    'path' => $path,
                    'url' => Storage::url($path),
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                ]
            ]);
        } catch (\Exception $e) {
            // Log failed upload
            ActivityLog::log('upload', [
                'file_name' => $request->file('file')?->getClientOriginalName(),
                'description' => 'Failed to upload file',
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete file
     */
    public function delete(Request $request)
    {
        $request->validate([
            'path' => 'required|string'
        ]);

        $path = $request->input('path');

        try {
            if (Storage::disk('public')->exists($path)) {
                // Get file info before deletion
                $fileName = basename($path);
                $fileSize = Storage::disk('public')->size($path);
                $fileType = Storage::disk('public')->mimeType($path);

                // Delete file
                Storage::disk('public')->delete($path);

                // Log activity
                ActivityLog::log('delete', [
                    'file_name' => $fileName,
                    'file_path' => $path,
                    'file_type' => $fileType,
                    'file_size' => $fileSize,
                    'description' => "Deleted file '{$fileName}'",
                    'metadata' => [
                        'directory' => dirname($path),
                    ],
                    'status' => 'success'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully'
                ]);
            }

            // Log failed delete - file not found
            ActivityLog::log('delete', [
                'file_path' => $path,
                'description' => 'Attempted to delete non-existent file',
                'status' => 'failed',
                'error_message' => 'File not found'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        } catch (\Exception $e) {
            // Log failed delete
            ActivityLog::log('delete', [
                'file_path' => $path,
                'description' => 'Failed to delete file',
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get file info for copy link
     */
    public function getFileInfo(Request $request)
    {
        $path = $request->input('path');

        if (!Storage::disk('public')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        // Log copy link activity
        ActivityLog::log('copy_link', [
            'file_name' => basename($path),
            'file_path' => $path,
            'file_type' => Storage::disk('public')->mimeType($path),
            'file_size' => Storage::disk('public')->size($path),
            'description' => "Copied link for file '" . basename($path) . "'",
            'metadata' => [
                'url' => Storage::url($path),
                'full_url' => url(Storage::url($path))
            ],
            'status' => 'success'
        ]);

        return response()->json([
            'success' => true,
            'url' => Storage::url($path),
            'full_url' => url(Storage::url($path))
        ]);
    }

    /**
     * Get files list (AJAX)
     */
    public function getFiles()
    {
        $files = $this->getAllFiles();
        return response()->json($files);
    }

    /**
     * Check if extension is image
     */
    private function isImage($extension)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'bmp'];
        return in_array(strtolower($extension), $imageExtensions);
    }

    /**
     * Get file type category
     */
    private function getFileType($file)
    {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if ($this->isImage($extension)) {
            return 'image';
        }

        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf'];
        if (in_array($extension, $documentExtensions)) {
            return 'document';
        }

        $archiveExtensions = ['zip', 'rar', '7z', 'tar', 'gz'];
        if (in_array($extension, $archiveExtensions)) {
            return 'archive';
        }

        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv'];
        if (in_array($extension, $videoExtensions)) {
            return 'video';
        }

        return 'other';
    }
}
