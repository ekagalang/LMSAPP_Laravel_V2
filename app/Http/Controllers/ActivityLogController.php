<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Category filter (groups of actions)
        $category = $request->query('category');
        if ($category) {
            switch ($category) {
                case 'http':
                    $query->where('action', 'like', 'http_%');
                    break;
                case 'model':
                    $query->where('action', 'like', 'model.%');
                    break;
                case 'instructor':
                    $query->where('action', 'like', 'instructor_%');
                    break;
                case 'event_organizer':
                    $query->where('action', 'like', 'event_organizer_%');
                    break;
                case 'participants':
                    $query->where('action', 'like', 'participants_%');
                    break;
            }
        }

        // Filter by action
        if ($request->has('action') && $request->action != '') {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search by file name or description
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('file_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Order by latest first
        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        // Get only Admin, EO, and Instructor users for filter dropdown
        $users = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['super-admin', 'admin', 'event-organizer', 'instructor']);
        })->select('id', 'name')->orderBy('name')->get();

        // Get unique actions for filter
        $actions = ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Categories available for quick filtering
        $categories = [
            'http' => 'HTTP Requests',
            'model' => 'Model Changes',
            'instructor' => 'Instructors',
            'event_organizer' => 'Event Organizers',
            'participants' => 'Participants',
        ];

        return view('activity-logs.index', compact('logs', 'users', 'actions', 'categories', 'category'));
    }

    /**
     * Show log details
     */
    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        return response()->json($log);
    }

    /**
     * Clear old logs
     */
    public function clear(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1'
        ]);

        $date = now()->subDays($request->days);
        $deleted = ActivityLog::where('created_at', '<', $date)->delete();

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deleted} old log(s)",
            'deleted' => $deleted
        ]);
    }

    /**
     * Export logs to CSV
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user');

        // Apply same filters as index
        if ($request->has('action') && $request->action != '') {
            $query->where('action', $request->action);
        }
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $filename = 'activity_logs_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID', 'User', 'Action', 'File Name', 'File Path',
                'File Type', 'File Size', 'IP Address', 'User Agent',
                'Description', 'Status', 'Error Message', 'Created At'
            ]);

            // Data rows
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user ? $log->user->name : 'N/A',
                    $log->action,
                    $log->file_name,
                    $log->file_path,
                    $log->file_type,
                    $log->file_size,
                    $log->ip_address,
                    $log->user_agent,
                    $log->description,
                    $log->status,
                    $log->error_message,
                    $log->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
