<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments (Admin/Instructor view)
     */
    public function index(Request $request)
    {
        // Check if user is admin/instructor
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Unauthorized access');
        }

        $query = Assignment::with(['creator', 'submissions'])
            ->where('created_by', Auth::id())
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'overdue':
                    $query->overdue();
                    break;
                case 'upcoming':
                    $query->upcoming();
                    break;
            }
        }

        $assignments = $query->paginate(10);

        return view('assignments.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new assignment
     */
    public function create()
    {
        // Check if user is admin/instructor
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Unauthorized access');
        }

        return view('assignments.create');
    }

    /**
     * Store a newly created assignment
     */
    public function store(Request $request)
    {
        // Check if user is admin/instructor
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor'])) {
            abort(403, 'Unauthorized access');
        }

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'submission_type' => 'required|in:file,link,both',
            'allowed_file_types' => 'nullable|array',
            'allowed_file_types.*' => 'string|in:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,mp4,mov,avi,mkv,mp3,wav,zip,rar',
            'max_file_size' => 'nullable|integer|min:1|max:1073741824', // 1GB in bytes
            'max_files' => 'nullable|integer|min:1|max:10',
            'due_date' => 'nullable|date|after:now',
            'allow_late_submission' => 'boolean',
            'late_submission_until' => 'nullable|date|after:due_date',
            'late_penalty' => 'nullable|numeric|min:0|max:100',
            'max_points' => 'required|integer|min:1|max:1000',
            'show_to_students' => 'boolean'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $assignment = Assignment::create([
                'title' => $request->title,
                'description' => $request->description,
                'instructions' => $request->instructions,
                'submission_type' => $request->submission_type,
                'allowed_file_types' => $request->allowed_file_types,
                'max_file_size' => $request->max_file_size,
                'max_files' => $request->max_files ?? 1,
                'due_date' => $request->due_date,
                'allow_late_submission' => $request->boolean('allow_late_submission'),
                'late_submission_until' => $request->late_submission_until,
                'late_penalty' => $request->late_penalty ?? 0,
                'max_points' => $request->max_points,
                'is_active' => true,
                'show_to_students' => $request->boolean('show_to_students', true),
                'created_by' => Auth::id(),
                'metadata' => []
            ]);

            return redirect()->route('assignments.show', $assignment)
                ->with('success', 'Assignment created successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create assignment: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified assignment
     */
    public function show(Assignment $assignment)
    {
        // Check permissions
        if (!Auth::user()) {
            abort(403, 'Unauthorized access');
        }

        $user = Auth::user();
        $isInstructor = $user->hasRole(['super-admin', 'instructor']);

        // Instructors can see all assignments they created
        if ($isInstructor && $assignment->created_by !== $user->id) {
            abort(403, 'You can only view assignments you created');
        }

        // Students can only see active and visible assignments
        if (!$isInstructor && (!$assignment->is_active || !$assignment->show_to_students)) {
            abort(404, 'Assignment not found');
        }

        $assignment->load(['creator', 'submissions.user', 'submissions.grader']);

        if ($isInstructor) {
            // Instructor view with all submissions
            $submissions = $assignment->submissions()->with('user')->latest()->get();
            return view('assignments.show-instructor', compact('assignment', 'submissions'));
        } else {
            // Student view
            $userSubmission = $assignment->getSubmissionForUser($user->id);
            return view('assignments.show-student', compact('assignment', 'userSubmission'));
        }
    }

    /**
     * Show the form for editing the specified assignment
     */
    public function edit(Assignment $assignment)
    {
        // Check if user is admin/instructor and owns the assignment
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor']) || $assignment->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return view('assignments.edit', compact('assignment'));
    }

    /**
     * Update the specified assignment
     */
    public function update(Request $request, Assignment $assignment)
    {
        // Check if user is admin/instructor and owns the assignment
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor']) || $assignment->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'submission_type' => 'required|in:file,link,both',
            'allowed_file_types' => 'nullable|array',
            'allowed_file_types.*' => 'string|in:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,jpg,jpeg,png,gif,mp4,mov,avi,mkv,mp3,wav,zip,rar',
            'max_file_size' => 'nullable|integer|min:1|max:1073741824',
            'max_files' => 'nullable|integer|min:1|max:10',
            'due_date' => 'nullable|date',
            'allow_late_submission' => 'boolean',
            'late_submission_until' => 'nullable|date|after:due_date',
            'late_penalty' => 'nullable|numeric|min:0|max:100',
            'max_points' => 'required|integer|min:1|max:1000',
            'is_active' => 'boolean',
            'show_to_students' => 'boolean'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $assignment->update([
                'title' => $request->title,
                'description' => $request->description,
                'instructions' => $request->instructions,
                'submission_type' => $request->submission_type,
                'allowed_file_types' => $request->allowed_file_types,
                'max_file_size' => $request->max_file_size,
                'max_files' => $request->max_files ?? 1,
                'due_date' => $request->due_date,
                'allow_late_submission' => $request->boolean('allow_late_submission'),
                'late_submission_until' => $request->late_submission_until,
                'late_penalty' => $request->late_penalty ?? 0,
                'max_points' => $request->max_points,
                'is_active' => $request->boolean('is_active'),
                'show_to_students' => $request->boolean('show_to_students'),
            ]);

            return redirect()->route('assignments.show', $assignment)
                ->with('success', 'Assignment updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update assignment: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified assignment
     */
    public function destroy(Assignment $assignment)
    {
        // Check if user is admin/instructor and owns the assignment
        if (!Auth::user() || !Auth::user()->hasRole(['super-admin', 'instructor']) || $assignment->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        try {
            $assignment->delete();
            return redirect()->route('assignments.index')
                ->with('success', 'Assignment deleted successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete assignment: ' . $e->getMessage()]);
        }
    }

    /**
     * Student assignments list
     */
    public function studentIndex()
    {
        $assignments = Assignment::active()
            ->visible()
            ->with(['creator'])
            ->latest()
            ->get();

        $userSubmissions = collect();
        if (Auth::check()) {
            $userSubmissions = AssignmentSubmission::where('user_id', Auth::id())
                ->with('assignment')
                ->get()
                ->keyBy('assignment_id');
        }

        return view('assignments.student-index', compact('assignments', 'userSubmissions'));
    }
}
