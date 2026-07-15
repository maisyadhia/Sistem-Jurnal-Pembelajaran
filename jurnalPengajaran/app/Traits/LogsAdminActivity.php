<?php

namespace App\Traits;

use App\Models\AdminLog;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

trait LogsAdminActivity
{
    protected function logActivity($action, $module = null, $description = null, $oldData = null, $newData = null)
    {
        try {
            // Cek apakah ini admin yang login
            $adminId = Session::get('admin_id');
            $adminName = Session::get('admin_name');
            $adminRole = Session::get('admin_role') ?? Session::get('user_role');
            
            // Jika bukan admin, jangan log
            if (!$adminId || !in_array($adminRole, ['admin', 'humas'])) {
                return;
            }

            AdminLog::create([
                'admin_id' => $adminId,
                'admin_name' => $adminName,
                'admin_role' => $adminRole,
                'action' => $action,
                'module' => $module,
                'description' => $description,
                'old_data' => $oldData ? json_encode($oldData) : null,
                'new_data' => $newData ? json_encode($newData) : null,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log error tapi jangan ganggu proses utama
            \Illuminate\Support\Facades\Log::error('Failed to log admin activity: ' . $e->getMessage());
        }
    }
}