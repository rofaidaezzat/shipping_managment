<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Exception;

class LogController extends Controller
{
    /**
     * Display a listing of system logs with search and level filtering.
     * Route: logs.index (Access: Super Admin only)
     */
    public function index(Request $request)
    {
        $query = SystemLog::with('creator')->latest('id');

        // Search filter
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Level filter
        if ($level = $request->input('level')) {
            $query->level($level);
        }

        // Date range filter
        if ($fromDate = $request->input('from_date')) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        if ($toDate = $request->input('to_date')) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Calculate summary counters for header badges/cards
        $metrics = [
            'total'    => SystemLog::count(),
            'errors'   => SystemLog::whereIn('level', ['error', 'critical', 'emergency', 'alert'])->count(),
            'warnings' => SystemLog::where('level', 'warning')->count(),
            'info'     => SystemLog::whereIn('level', ['info', 'notice', 'debug'])->count(),
        ];

        return view('admin.logs.index', compact('logs', 'metrics'));
    }

    /**
     * Display or return JSON for a single log entry (stack trace & full details).
     */
    public function show(SystemLog $log)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'log'     => [
                    'id'          => $log->id,
                    'level'       => $log->level,
                    'badge_class' => $log->badge_class,
                    'message'     => $log->message,
                    'exception'   => $log->exception,
                    'file'        => $log->file,
                    'line'        => $log->line,
                    'stack_trace' => $log->stack_trace,
                    'user'        => $log->user ?? 'Guest / System',
                    'user_id'     => $log->user_id,
                    'company'     => $log->company ?? '—',
                    'url'         => $log->url ?? '—',
                    'method'      => $log->method ?? '—',
                    'route_name'  => $log->route_name ?? '—',
                    'ip'          => $log->ip ?? '—',
                    'created_at'  => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : '—',
                    'time_ago'    => $log->created_at ? $log->created_at->diffForHumans() : '—',
                ],
            ]);
        }

        return view('admin.logs.show', compact('log'));
    }

    /**
     * Delete a specific log entry.
     */
    public function destroy(SystemLog $log)
    {
        $log->delete();

        return redirect()->route('logs.index')->with('success', "Log entry #{$log->id} deleted successfully.");
    }

    /**
     * Clear all system logs.
     */
    public function clear()
    {
        SystemLog::query()->truncate();

        return redirect()->route('logs.index')->with('success', 'All system logs have been cleared successfully.');
    }

    /**
     * Trigger a demonstration test log to verify log capturing.
     */
    public function test(Request $request)
    {
        $type = $request->query('type', 'exception');

        if ($type === 'exception') {
            try {
                // Simulate an exception
                throw new \RuntimeException("This is a verified test exception triggered from System Logs dashboard.", 500);
            } catch (\Throwable $e) {
                SystemLog::captureException($e, 'error');
            }
        } elseif ($type === 'warning') {
            SystemLog::record('warning', 'Test warning: Simulated high disk usage or performance throttle threshold reached.', [
                'company'    => 'Global Logistics Co.',
                'route_name' => 'logs.test',
            ]);
        } else {
            SystemLog::record('info', 'Test info: Scheduled maintenance routine executed successfully.', [
                'route_name' => 'logs.test',
            ]);
        }

        return redirect()->route('logs.index')->with('success', 'Test log entry generated and recorded successfully!');
    }
}
