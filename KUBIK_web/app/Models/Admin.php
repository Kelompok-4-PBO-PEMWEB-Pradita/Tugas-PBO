<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admins';
    protected $primaryKey = 'id_admin';
    protected $fillable = ['name', 'email', 'password'];
    public $timestamps = true;

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'id_admin');
    }

    public function notifications()
    {
        return $this->hasMany(AdminNotification::class, 'id_admin');
    }
}
