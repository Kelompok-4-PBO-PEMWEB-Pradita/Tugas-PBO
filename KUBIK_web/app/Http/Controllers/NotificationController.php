<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\AdminNotification;

class NotificationController extends Controller
{
    public function userNotifications($id_user) {
        return Notification::where('id_user',$id_user)->orderByDesc('created_at')->get();
    }

    public function markAllRead(Request $req, $id_user) {
        Notification::where('id_user',$id_user)->update(['is_read'=>true]);
        return response()->json(['message'=>'All notifications marked read']);
    }

    public function adminNotifications($id_admin) {
        return AdminNotification::where('id_admin',$id_admin)->orderByDesc('created_at')->get();
    }
}
