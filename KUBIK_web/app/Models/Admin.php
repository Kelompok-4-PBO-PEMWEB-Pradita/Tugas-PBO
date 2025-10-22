<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins';
    protected $primaryKey = 'id_admin';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true; // admins table has created_at, updated_at

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password'
    ];

    // RELATIONS
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'id_admin', 'id_admin');
    }

    public function adminNotifications(): HasMany
    {
        return $this->hasMany(AdminNotification::class, 'id_admin', 'id_admin');
    }

    // SMART LOGIC HELPERS
    // Approve a booking (set status approved, set actual_start, mark assets borrowed)
    public function approveBooking(int $bookingId): bool
    {
        return Booking::approveByAdmin($bookingId, $this->id_admin);
    }

    // Verify return (mark completed and allow admin to set asset conditions)
    public function verifyReturn(int $bookingId): bool
    {
        return Booking::verifyReturnByAdmin($bookingId, $this->id_admin);
    }
}
