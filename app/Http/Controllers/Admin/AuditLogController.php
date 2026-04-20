<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('model')) {
            $query->where('auditable_type', 'like', '%' . $request->model . '%');
        }

        if ($request->filled('auditable_id')) {
            $query->where('auditable_id', $request->auditable_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $totalLogs = AuditLog::count();
        $stats = [
            'created' => AuditLog::where('event', 'created')->count(),
            'updated' => AuditLog::where('event', 'updated')->count(),
            'deleted' => AuditLog::where('event', 'deleted')->count(),
        ];

        $logs = $query->paginate(20);
        $users = \App\Models\User::all();

        return view('admin.audit-logs.index', compact('logs', 'users', 'totalLogs', 'stats'));
    }

    public function show($id)
    {
        $log = AuditLog::with('user')->findOrFail($id);
        return view('admin.audit-logs.show', compact('log'));
    }

    public function export(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Apply same filters as index
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('event')) $query->where('event', $request->event);
        if ($request->filled('model')) $query->where('auditable_type', 'like', '%' . $request->model . '%');
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $logs = $query->get();
        
        $filename = "audit_logs_" . date('Y-m-d_H-i-s') . ".csv";
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // CSV Headers
        fputcsv($handle, ['ID', 'Timestamp', 'User', 'Event', 'Model', 'Record ID', 'IP Address', 'Old Values', 'New Values']);

        foreach ($logs as $log) {
            fputcsv($handle, [
                $log->id,
                $log->created_at,
                $log->user->name ?? 'System',
                $log->event,
                class_basename($log->auditable_type),
                $log->auditable_id,
                $log->ip_address,
                json_encode($log->old_values),
                json_encode($log->new_values)
            ]);
        }

        fclose($handle);
        exit;
    }
}
