<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Type extends Model
{
    protected $table = 'types';
    protected $primaryKey = 'id_type';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // types table has created_at only

    protected $fillable = [
        'id_type',
        'name'
    ];

    public function assetMasters(): HasMany
    {
        return $this->hasMany(AssetMaster::class, 'id_type', 'id_type');
    }

    // SMART LOGIC: generate id_type in format CAT-000001 if not set
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id_type)) {
                // generate next numeric by counting current rows
                $count = static::query()->count() + 1;
                $model->id_type = 'CAT-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
