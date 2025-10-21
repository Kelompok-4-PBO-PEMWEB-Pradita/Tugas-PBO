<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'id_booking';
    protected $fillable = [
        'id_user', 'id_admin', 'start_time', 'end_time', 'actual_start',
        'return_at', 'late_return', 'note', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }

    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'booking_assets', 'id_booking', 'id_asset');
    }
}
