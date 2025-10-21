<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAsset extends Model
{
    protected $table = 'booking_assets';
    protected $primaryKey = 'id_booking_asset';
    protected $fillable = ['id_booking', 'id_asset'];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'id_booking');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'id_asset');
    }
}
