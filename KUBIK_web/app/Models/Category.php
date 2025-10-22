<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id_category';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // categories table has created_at only

    protected $fillable = [
        'id_category',
        'name'
    ];

    public function assetMasters(): HasMany
    {
        return $this->hasMany(AssetMaster::class, 'id_category', 'id_category');
    }

    // SMART LOGIC: generate id_category in format CAT-000001 if not set
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id_category)) {
                // generate next numeric by counting current rows
                $count = static::query()->count() + 1;
                $model->id_category = 'CAT-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
