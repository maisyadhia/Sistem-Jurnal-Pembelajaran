<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request)
    {
        $logs = AdminLog::with('admin')
            ->when($request->action, function($query, $action) {
                return $query->where('action', $action);
            })
            ->when($request->module, function($query, $module) {
                return $query->where('module', $module);
            })
            ->when($request->date_from, function($query, $date) {
                return $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function($query, $date) {
                return $query->whereDate('created_at', '<=', $date);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $actions = AdminLog::distinct('action')->pluck('action');
        $modules = AdminLog::distinct('module')->pluck('module');

        return view('admin.logs.index', compact('logs', 'actions', 'modules'));
    }

    public function show($id)
    {
        $log = AdminLog::with('admin')->findOrFail($id);
        return view('admin.logs.show', compact('log'));
    }

    public function clear()
    {
        $this->logActivity(
            'delete_all',
            'logs',
            "Menghapus semua log aktivitas",
            null,
            ['total_deleted' => AdminLog::count()]
        );

        AdminLog::truncate();
        
        return redirect()->route('admin.logs')
            ->with('success', 'Semua log berhasil dihapus!');
    }
}