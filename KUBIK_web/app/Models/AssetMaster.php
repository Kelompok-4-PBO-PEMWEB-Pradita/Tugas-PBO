<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class AssetMaster extends Model
{
    protected $table = 'asset_masters';
    protected $primaryKey = 'id_master';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // created_at only per schema

    protected $fillable = [
        'id_master',
        'id_category',
        'id_type',
        'name',
        'stock_total',
        'stock_available'
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'id_master', 'id_master');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'id_type', 'id_type');
    }

    // SMART LOGIC: generate id_master and auto-generate assets on create
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id_master)) {
                $count = static::query()->count() + 1;
                $model->id_master = 'AM-' . str_pad($count, 6, '0', STR_PAD_LEFT);
            }
            // ensure stock_available defaults to stock_total if not provided
            if (!isset($model->stock_available) && isset($model->stock_total)) {
                $model->stock_available = $model->stock_total;
            }
        });

        // After created, auto generate asset units based on stock_total
        static::created(function ($model) {
            if (!empty($model->stock_total) && $model->stock_total > 0) {
                DB::transaction(function () use ($model) {
                    $toCreate = $model->stock_total - $model->assets()->count();
                    for ($i = 0; $i < $toCreate; $i++) {
                        Asset::create([
                            'id_master' => $model->id_master,
                            'asset_condition' => 'good',
                            'status' => 'available'
                        ]);
                    }
                    // sync stock values
                    $total = Asset::where('id_master', $model->id_master)->count();
                    $available = Asset::where('id_master', $model->id_master)->where('status','available')->count();
                    $model->stock_total = $total;
                    $model->stock_available = $available;
                    $model->saveQuietly(); // avoid recursion
                });
            }
        });
    }

    // Helper to recalc stock (can be called from Asset model)
    public function recalcStock(): void
    {
        $total = $this->assets()->count();
        $available = $this->assets()->where('status', 'available')->count();
        $this->stock_total = $total;
        $this->stock_available = $available;
        $this->saveQuietly();
    }
}
