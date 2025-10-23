<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AdminNotificationController extends Controller
{
    /**
     * Tampilkan seluruh notifikasi untuk admin tertentu
     */
    public function index($id_admin)
    {
        $admin = Admin::find($id_admin);
        if (!$admin) {
            return response()->json(['message' => 'Admin tidak ditemukan'], 404);
        }

        $notifications = Notification::where('id_admin', $id_admin)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'message' => 'Daftar notifikasi admin ' . $admin->name,
            'data' => $notifications
        ]);
    }

    /**
     * Tandai seluruh notifikasi admin sebagai dibaca
     */
    public function markAllAsRead($id_admin)
    {
        $count = Notification::where('id_admin', $id_admin)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message' => 'Semua notifikasi admin ditandai telah dibaca',
            'updated_count' => $count
        ]);
    }

    /**
     * Hapus semua notifikasi yang sudah dibaca dan lebih dari 14 hari
     */
    public function clearOldNotifications($id_admin)
    {
        $deleted = Notification::where('id_admin', $id_admin)
            ->where('is_read', true)
            ->where('created_at', '<', Carbon::now()->subDays(14))
            ->delete();

        return response()->json([
            'message' => 'Notifikasi lama berhasil dibersihkan',
            'deleted_count' => $deleted
        ]);
    }
}
