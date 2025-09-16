<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AssignmentSubmissionController extends Controller
{
    /**
     * Show submission form for student
     */
    public function create(Assignment $assignment)
    {
        if (!Auth::check() || !$assignment->canSubmit()) {
            abort(403, 'Submission not allowed');
        }

        $existingSubmission = $assignment->getSubmissionForUser(Auth::id());

        if ($existingSubmission && !$existingSubmission->canEdit()) {
            return redirect()->route('assignments.submissions.show', [$assignment, $existingSubmission])
                ->with('info', 'You have already submitted this assignment.');
        }

        return view('assignments.submissions.create', compact('assignment', 'existingSubmission'));
    }

    /**
     * Store assignment submission
     */
    public function store(Request $request, Assignment $assignment)
    {
        // Increase memory and upload limits for large file uploads
        ini_set('memory_limit', '512M');
        ini_set('post_max_size', '300M');
        ini_set('upload_max_filesize', '200M');
        ini_set('max_execution_time', 300);

        if (!Auth::check() || !$assignment->canSubmit()) {
            abort(403, 'Submission not allowed');
        }

        $userId = Auth::id();

        // Check if user already has a submission
        $existingSubmission = $assignment->getSubmissionForUser($userId);
        if ($existingSubmission && !$existingSubmission->canEdit()) {
            return back()->withErrors(['error' => 'You have already submitted this assignment.']);
        }

        // Build validation rules
        $rules = [
            'submission_text' => 'nullable|string|max:10000',
            'submission_link' => 'nullable|url|max:500',
        ];

        // File validation
        if ($assignment->submission_type === 'file' || $assignment->submission_type === 'both') {
            $maxFiles = $assignment->max_files ?: 1;
            $rules['files'] = 'nullable|array|max:' . $maxFiles;
            $rules['files.*'] = 'file|max:' . ($assignment->max_file_size ? ($assignment->max_file_size / 1024) : 51200); // Convert to KB

            if ($assignment->allowed_file_types && !empty($assignment->allowed_file_types)) {
                $mimeTypes = $this->getAceptedMimeTypes($assignment->allowed_file_types);
                $rules['files.*'] .= '|mimes:' . implode(',', $assignment->allowed_file_types);
            }
        }

        // Conditional validation based on submission type
        if ($assignment->submission_type === 'file') {
            $rules['files'] = str_replace('nullable', 'required', $rules['files'] ?? 'required');
        } elseif ($assignment->submission_type === 'link') {
            $rules['submission_link'] = 'required|url|max:500';
        } elseif ($assignment->submission_type === 'both') {
            $rules['files_or_link'] = 'required_without_all:files,submission_link';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Handle file uploads
            $filePaths = [];
            $fileMetadata = [];

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('assignments/submissions/' . $assignment->id, $fileName, 'public');

                    $filePaths[] = $filePath;
                    $fileMetadata[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'extension' => $file->getClientOriginalExtension()
                    ];
                }
            }

            // Create or update submission
            $submissionData = [
                'assignment_id' => $assignment->id,
                'user_id' => $userId,
                'submission_text' => $request->submission_text,
                'submission_link' => $request->submission_link,
                'file_paths' => $filePaths ?: null,
                'file_metadata' => $fileMetadata ?: null,
                'status' => 'draft',
                'attempt_number' => 1
            ];

            if ($existingSubmission) {
                // Delete old files if replacing
                if ($existingSubmission->file_paths && $filePaths) {
                    $existingSubmission->deleteFiles();
                }
                $existingSubmission->update($submissionData);
                $submission = $existingSubmission;
            } else {
                $submission = AssignmentSubmission::create($submissionData);
            }

            // Auto-submit if requested
            if ($request->boolean('submit_now')) {
                $submission->submit();
            }

            DB::commit();

            $message = $request->boolean('submit_now')
                ? 'Assignment submitted successfully!'
                : 'Draft saved successfully!';

            return redirect()->route('assignments.submissions.show', [$assignment, $submission])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();

            // Clean up uploaded files on error
            if (!empty($filePaths)) {
                foreach ($filePaths as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            return back()->withErrors(['error' => 'Failed to save submission: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show submission details
     */
    public function show(Assignment $assignment, AssignmentSubmission $submission)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user) {
            abort(403, 'Unauthorized access');
        }

        $isInstructor = $user->hasRole(['super-admin', 'instructor']);
        $isOwner = $submission->user_id === $user->id;

        if (!$isInstructor && !$isOwner) {
            abort(403, 'You can only view your own submissions');
        }

        if ($isInstructor && $assignment->created_by !== $user->id) {
            abort(403, 'You can only view submissions for your assignments');
        }

        $submission->load(['user', 'grader']);

        return view('assignments.submissions.show', compact('assignment', 'submission', 'isInstructor'));
    }

    /**
     * Download submission file
     */
    public function downloadFile(Assignment $assignment, AssignmentSubmission $submission, $fileIndex)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user) {
            abort(403, 'Unauthorized access');
        }

        $isInstructor = $user->hasRole(['super-admin', 'instructor']);
        $isOwner = $submission->user_id === $user->id;

        if (!$isInstructor && !$isOwner) {
            abort(403, 'You can only download your own submission files');
        }

        if ($isInstructor && $assignment->created_by !== $user->id) {
            abort(403, 'You can only download files from your assignments');
        }

        if (!$submission->file_paths || !isset($submission->file_paths[$fileIndex])) {
            abort(404, 'File not found');
        }

        $filePath = $submission->file_paths[$fileIndex];
        $fileName = $submission->file_metadata[$fileIndex]['original_name'] ?? basename($filePath);

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File not found on server');
        }

        return Storage::disk('public')->download($filePath, $fileName);
    }

    /**
     * Grade submission (Instructor only)
     */
    public function grade(Request $request, Assignment $assignment, AssignmentSubmission $submission)
    {
        $user = Auth::user();

        // Check permissions
        if (!$user || !$user->hasRole(['super-admin', 'instructor']) || $assignment->created_by !== $user->id) {
            abort(403, 'Unauthorized access');
        }

        $rules = [
            'points_earned' => 'required|integer|min:0|max:' . $assignment->max_points,
            'instructor_feedback' => 'nullable|string|max:5000',
            'status' => 'required|in:graded,returned'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $percentage = ($request->points_earned / $assignment->max_points) * 100;

            $submission->update([
                'points_earned' => $request->points_earned,
                'grade' => $percentage,
                'instructor_feedback' => $request->instructor_feedback,
                'status' => $request->status,
                'graded_by' => $user->id,
                'graded_at' => now(),
                'grade_metadata' => [
                    'graded_at' => now()->toISOString(),
                    'grader_name' => $user->name,
                    'grader_email' => $user->email
                ]
            ]);

            return back()->with('success', 'Submission graded successfully!');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to grade submission: ' . $e->getMessage()]);
        }
    }

    /**
     * Get accepted mime types for file extensions
     */
    private function getAceptedMimeTypes($extensions)
    {
        $mimeMap = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'mkv' => 'video/x-matroska',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'zip' => 'application/zip',
            'rar' => 'application/vnd.rar'
        ];

        return array_intersect_key($mimeMap, array_flip($extensions));
    }
}
