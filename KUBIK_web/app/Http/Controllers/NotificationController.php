<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Tampilkan semua notifikasi
     * Smart Logic: hanya menampilkan 30 notifikasi terakhir
     */
    public function index()
    {
        $notifications = Notification::orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'message' => 'Daftar notifikasi terbaru',
            'data' => $notifications
        ]);
    }

    /**
     * Tampilkan notifikasi berdasarkan admin
     */
    public function showByAdmin($id_admin)
    {
        $notifications = Notification::where('id_admin', $id_admin)
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        if ($notifications->isEmpty()) {
            return response()->json(['message' => 'Tidak ada notifikasi untuk admin ini']);
        }

        return response()->json(['data' => $notifications]);
    }

    /**
     * Tandai notifikasi sudah dibaca
     * Smart Logic: ubah is_read menjadi true
     */
    public function markAsRead($id)
    {
        $notif = Notification::find($id);

        if (!$notif) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notif->update(['is_read' => true]);

        return response()->json(['message' => 'Notifikasi ditandai sebagai sudah dibaca']);
    }

    /**
     * Hapus notifikasi (hanya notifikasi lama)
     * Smart Logic: hanya bisa hapus jika lebih dari 7 hari
     */
    public function destroy($id)
    {
        $notif = Notification::find($id);

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

    /**
     * Smart System tambahan:
     * Bersihkan otomatis notifikasi yang berusia > 30 hari
     */
    public function autoCleanup()
    {
        $deleted = Notification::where('created_at', '<', Carbon::now()->subDays(30))->delete();

        return response()->json([
            'message' => 'Pembersihan otomatis selesai',
            'deleted_count' => $deleted
        ]);
    }
}
