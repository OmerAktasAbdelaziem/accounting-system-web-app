<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of audit logs
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by user name or model ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('model_id', 'like', "%{$search}%")
                ->orWhere('model_type', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(50);
        $users = \App\Models\User::orderBy('name')->get();
        $actions = AuditLog::distinct('action')->pluck('action');
        $modelTypes = AuditLog::distinct('model_type')->pluck('model_type');

        return view('audit-logs.index', compact('logs', 'users', 'actions', 'modelTypes'));
    }

    /**
     * Display a specific audit log entry
     */
    public function show(AuditLog $auditLog)
    {
        return view('audit-logs.show', compact('auditLog'));
    }

    /**
     * Export audit logs to CSV
     */
    public function export(Request $request)
    {
        $this->authorizeDownloads($request);

        $query = AuditLog::with('user')
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->get();

        // Create CSV
        $filename = 'audit-logs-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['ID', 'User', 'Action', 'Model Type', 'Model ID', 'Date', 'Time', 'IP Address']);
            
            // Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->id,
                    $log->user?->name ?? 'System',
                    $log->action,
                    $log->model_type,
                    $log->model_id,
                    $log->created_at->format('Y-m-d'),
                    $log->created_at->format('H:i:s'),
                    $log->ip_address,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
