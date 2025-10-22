<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id_notif';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // created_at only

    protected $fillable = [
        'id_user',
        'message',
        'is_read',
        'created_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // SMART LOGIC helper
    public static function sendToUser(int $userId, string $message)
    {
        return static::create([
            'id_user' => $userId,
            'message' => $message,
            'is_read' => false,
            'created_at' => now()
        ]);
    }
}
