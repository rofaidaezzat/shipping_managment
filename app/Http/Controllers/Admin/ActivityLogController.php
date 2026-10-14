<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of transaction & user activity logs.
     * Route: activity_logs.index
     */
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'creator', 'subject'])->latest('id');

        // Search filter (description, subject ID/name, causer name/email, created_by, properties)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%")
                  ->orWhere('subject_type', 'like', "%{$search}%")
                  ->orWhere('created_by', 'like', "%{$search}%")
                  ->orWhere('causer_name', 'like', "%{$search}%")
                  ->orWhere('causer_email', 'like', "%{$search}%")
                  ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        // Event filter (created, updated, deleted, etc.)
        if ($event = $request->input('event')) {
            $query->where('event', $event);
        }

        // Subject type filter (e.g., Invoice, Receipt, Container, Port, Company, User)
        if ($subjectType = $request->input('subject_type')) {
            $query->where('subject_type', 'like', "%{$subjectType}%");
        }

        // Causer / Created By filter
        if ($causerId = $request->input('causer_id')) {
            $selectedUser = User::find($causerId);
            $query->where(function ($q) use ($causerId, $selectedUser) {
                $q->where('causer_id', $causerId);
                if ($selectedUser) {
                    $q->orWhere('created_by', $selectedUser->name);
                }
            });
        }

        // Date range filters
        if ($fromDate = $request->input('from_date')) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate = $request->input('to_date')) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $activities = $query->paginate(20)->withQueryString();

        // Summary metrics
        $metrics = [
            'total'   => Activity::count(),
            'created' => Activity::where('event', 'created')->count(),
            'updated' => Activity::where('event', 'updated')->count(),
            'deleted' => Activity::where('event', 'deleted')->count(),
        ];

        // Unique subject types and users for dropdown filters
        $users = User::orderBy('name')->get(['id', 'name', 'email', 'role']);
        $subjectTypes = [
            'Invoice'   => 'Invoices',
            'Receipt'   => 'Receipts',
            'Container' => 'Containers',
            'Port'      => 'Ports',
            'Company'   => 'Companies',
            'User'      => 'Users',
        ];

        return view('admin.activity_logs.index', compact('activities', 'metrics', 'users', 'subjectTypes'));
    }

    /**
     * Show detailed JSON or HTML view of a specific activity log entry.
     */
    public function show(Activity $activity)
    {
        $activity->load(['causer', 'creator', 'subject']);
        $causer = $activity->causer ?? $activity->creator;

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success'  => true,
                'activity' => [
                    'id'           => $activity->id,
                    'log_name'     => $activity->log_name,
                    'description'  => $activity->description,
                    'event'        => $activity->event,
                    'subject_type' => class_basename($activity->subject_type ?? ''),
                    'subject_id'   => $activity->subject_id,
                    'created_by'   => $activity->created_by,
                    'causer_name'  => $causer?->name ?? 'System',
                    'causer_email' => $causer?->email ?? '—',
                    'causer_role'  => $causer?->role ?? 'System',
                    'properties'   => $activity->properties,
                    'changes'      => $activity->changes(),
                    'created_at'   => $activity->created_at?->format('M d, Y H:i:s'),
                    'time_ago'     => $activity->created_at?->diffForHumans(),
                ],
            ]);
        }

        return view('admin.activity_logs.show', compact('activity'));
    }

    /**
     * Clear all activity logs (Super Admin only).
     */
    public function clear()
    {
        Activity::truncate();

        return redirect()->route('activity_logs.index')->with('success', 'All activity logs have been cleared successfully.');
    }
}
