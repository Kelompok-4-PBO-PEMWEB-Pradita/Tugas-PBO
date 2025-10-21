<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets';
    protected $primaryKey = 'id_asset';
    protected $fillable = ['id_master', 'status', 'condition'];

    public function master()
    {
        return $this->belongsTo(AssetMaster::class, 'id_master');
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_assets', 'id_asset', 'id_booking');
    }
}
