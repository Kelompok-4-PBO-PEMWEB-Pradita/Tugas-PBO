<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Type extends Model
{
    protected $table = 'types';
    protected $primaryKey = 'id_type';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // types table has created_at only

    protected $fillable = [
        'id_type',
        'id_category',
        'name'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }

    public function assetMasters(): HasMany
    {
        return $this->hasMany(AssetMaster::class, 'id_type', 'id_type');
    }

    // SMART LOGIC: generate id_type TYP-000001 if not provided
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id_type)) {
                $count = static::query()->count() + 1;
                $model->id_type = 'TYP-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
