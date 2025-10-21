<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $table = 'admin_notifications';
    protected $primaryKey = 'id_notification';
    protected $fillable = ['id_admin', 'message', 'is_read'];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }
}
