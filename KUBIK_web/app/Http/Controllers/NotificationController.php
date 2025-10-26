<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class NotificationController extends Controller
{
    // tampilkan 30 notifikasi terakhir milik user tertentu
    public function index($id_user)
    {
        $notifications = Notification::where('id_user', $id_user)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'message' => 'Daftar notifikasi untuk user ID: ' . $id_user,
            'data' => $notifications
        ]);
    }

    // tandai notifikasi user sudah dibaca
    public function markAsRead($id_notif)
    {
        $notif = Notification::find($id_notif);
        if (!$notif) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notif->update(['is_read' => true]);

        return response()->json(['message' => 'Notifikasi berhasil ditandai sebagai dibaca']);
    }

    // hapus notifikasi user jika > 7 hari
    public function destroy($id_notif)
    {
        $notif = Notification::find($id_notif);
        if (!$notif) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $createdAt = Carbon::parse($notif->created_at);
        if ($createdAt->diffInDays(Carbon::now()) < 7) {
            return response()->json(['message' => 'Notifikasi belum dapat dihapus (minimal 7 hari)'], 403);
        }

        $notif->delete();
        return response()->json(['message' => 'Notifikasi berhasil dihapus']);
    }

    // pembersihan otomatis notifikasi > 30 hari
    public function autoCleanup()
    {
        $deleted = Notification::where('created_at', '<', Carbon::now()->subDays(30))->delete();

        return response()->json([
            'message' => 'Pembersihan otomatis selesai',
            'deleted_count' => $deleted
        ]);
    }
}
