<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table = 'types';
    protected $primaryKey = 'id_type';
    public $incrementing = false;
    protected $fillable = ['id_type', 'name'];
    public $timestamps = true;

    public function assetMasters()
    {
        return $this->hasMany(AssetMaster::class, 'id_type');
    }
}
