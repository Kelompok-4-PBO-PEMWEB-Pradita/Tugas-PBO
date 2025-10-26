<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AdminNotificationController extends Controller
{
    // tampilkan semua notifikasi untuk admin
    public function index($id_admin)
    {
        $notifications = AdminNotification::where('id_admin', $id_admin)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'message' => 'Daftar notifikasi admin ID: ' . $id_admin,
            'data' => $notifications
        ]);
    }

    // tandai seluruh notifikasi admin dibaca
    public function markAllAsRead($id_admin)
    {
        $count = AdminNotification::where('id_admin', $id_admin)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message' => 'Semua notifikasi admin ditandai telah dibaca',
            'updated_count' => $count
        ]);
    }

    // hapus notifikasi lama (>14 hari dan sudah dibaca)
    public function clearOldNotifications($id_admin)
    {
        $deleted = AdminNotification::where('id_admin', $id_admin)
            ->where('is_read', true)
            ->where('created_at', '<', Carbon::now()->subDays(14))
            ->delete();

        return response()->json([
            'message' => 'Notifikasi lama berhasil dibersihkan',
            'deleted_count' => $deleted
        ]);
    }
}
