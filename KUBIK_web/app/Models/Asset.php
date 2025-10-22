<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets';
    protected $primaryKey = 'id_asset';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // assets has created_at only per final request

    protected $fillable = [
        'id_master',
        'asset_condition',
        'status',
        'created_at'
    ];

    public function master()
    {
        return $this->belongsTo(AssetMaster::class, 'id_master', 'id_master');
    }

    public function bookings()
    {
        // many-to-many via booking_assets pivot (no id on pivot)
        return $this->belongsToMany(Booking::class, 'booking_assets', 'id_asset', 'id_booking')
                    ->withPivot('created_at');
    }

    // SMART LOGIC: when asset status changes, recalc stock on master
    protected static function booted()
    {
        static::updated(function ($asset) {
            if ($asset->id_master) {
                $asset->master->recalcStock();
            }
        });

        static::created(function ($asset) {
            if ($asset->id_master) {
                $asset->master->recalcStock();
            }
        });

        static::deleted(function ($asset) {
            if ($asset->id_master) {
                $asset->master->recalcStock();
            }
        });
    }
}
