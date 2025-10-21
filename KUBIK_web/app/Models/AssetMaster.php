<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMaster extends Model
{
    protected $table = 'asset_masters';
    protected $primaryKey = 'id_master';
    public $incrementing = false;
    protected $fillable = [
        'id_master', 'name', 'description', 'id_category', 'id_type',
        'stock_total', 'stock_available'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'id_type');
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'id_master');
    }
}
