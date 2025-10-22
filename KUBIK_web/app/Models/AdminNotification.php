<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $table = 'admin_notifications';
    protected $primaryKey = 'id_notif_admin';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // created_at only

    protected $fillable = [
        'id_admin',
        'message',
        'is_read',
        'created_at'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    public static function sendToAdmin(?int $adminId, string $message)
    {
        return static::create([
            'id_admin' => $adminId,
            'message' => $message,
            'is_read' => false,
            'created_at' => now()
        ]);
    }
}
