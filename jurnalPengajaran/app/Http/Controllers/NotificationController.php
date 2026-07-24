<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Tandai Notifikasi Sebagai Sudah Dibaca (is_read = 1)
     */
    public function markAsRead($id)
    {
        DB::table('notifications')
            ->where('id', $id)
            ->update(['is_read' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil ditandai dibaca.'
        ]);
    }
}