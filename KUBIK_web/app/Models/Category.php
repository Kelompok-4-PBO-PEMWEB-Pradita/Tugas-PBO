<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id_category';
    public $incrementing = false;
    protected $fillable = ['id_category', 'name'];
    public $timestamps = true;

    public function assetMasters()
    {
        return $this->hasMany(AssetMaster::class, 'id_category');
    }
}
