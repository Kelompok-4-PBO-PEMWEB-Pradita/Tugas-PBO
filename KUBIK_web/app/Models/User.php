<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true; // users table has created_at, updated_at

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number'
    ];

    protected $hidden = [
        'password'
    ];

    // RELATIONS
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'id_user', 'id_user');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'id_user', 'id_user');
    }

    // SMART LOGIC HELPERS (model-level helpers to be called by controllers/services)
    public function submitBooking(array $assetIds, string $startTime, string $endTime): Booking
    {
        // Create new booking and attach asset ids via Booking model static helper
        return Booking::createForUser($this->id_user, $assetIds, $startTime, $endTime);
    }
}
