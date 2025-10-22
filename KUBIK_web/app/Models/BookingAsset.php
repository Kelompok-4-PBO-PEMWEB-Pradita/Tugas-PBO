<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAsset extends Model
{
    protected $table = 'booking_assets';
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false; // created_at handled manually if present

    protected $fillable = [
        'id_booking',
        'id_asset',
        'created_at'
    ];
}
