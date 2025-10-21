<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    protected $fillable = ['name', 'email', 'phone', 'password', 'role'];
    public $timestamps = true;

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'id_user');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'id_user');
    }
}
