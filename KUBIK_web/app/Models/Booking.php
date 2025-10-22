<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'id_booking';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true; // created_at, updated_at exist

    protected $fillable = [
        'id_user',
        'id_admin',
        'start_time',
        'end_time',
        'return_at',
        'late_return',
        'status'
    ];

    // relations
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin', 'id_admin');
    }

    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'booking_assets', 'id_booking', 'id_asset')
                    ->withPivot('created_at');
    }

    // SMART LOGIC: create booking with multiple assets
    public static function createForUser(int $userId, array $assetIds, string $startTime, string $endTime): self
    {
        return DB::transaction(function () use ($userId, $assetIds, $startTime, $endTime) {
            // validate assets availability
            $availableCount = Asset::whereIn('id_asset', $assetIds)->where('status', 'available')->count();
            if ($availableCount !== count($assetIds)) {
                throw new \Exception('Some assets are not available for booking');
            }

            $booking = static::create([
                'id_user' => $userId,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'pending'
            ]);

            // attach assets (insert into booking_assets pivot)
            foreach ($assetIds as $aid) {
                DB::table('booking_assets')->insert([
                    'id_booking' => $booking->id_booking,
                    'id_asset' => $aid,
                    'created_at' => now()
                ]);
            }

            // create admin notification (handled elsewhere by Notification system/controller)
            return $booking;
        });
    }

    // Approve by admin: set status approved, set id_admin, set actual_start = now and mark assets as borrowed
    public static function approveByAdmin(int $bookingId, int $adminId): bool
    {
        return DB::transaction(function () use ($bookingId, $adminId) {
            $booking = static::findOrFail($bookingId);
            $booking->status = 'approved';
            $booking->id_admin = $adminId;
            // set actual_start to now as per discussion? (we removed actual_start earlier but in final we kept return_at only)
            // Based on previous final: we do not have actual_start; only record admin id
            $booking->save();

            // set assets to borrowed
            $assetIds = DB::table('booking_assets')->where('id_booking', $bookingId)->pluck('id_asset')->toArray();
            Asset::whereIn('id_asset', $assetIds)->update(['status' => 'borrowed']);

            // recalc master stocks
            $masters = Asset::whereIn('id_asset', $assetIds)->select('id_master')->distinct()->pluck('id_master')->toArray();
            foreach ($masters as $m) {
                $am = AssetMaster::find($m);
                if ($am) $am->recalcStock();
            }

            return true;
        });
    }

    // User requests return: set return_at and set status back to pending (await admin verification)
    public function requestReturn(): bool
    {
        return DB::transaction(function () {
            $this->return_at = Carbon::now();
            // calculate late_return in hours
            $this->late_return = max(0, Carbon::parse($this->return_at)->diffInHours(Carbon::parse($this->end_time)));
            $this->status = 'pending';
            $this->save();

            // notify admin implemented in controller/notification system
            return true;
        });
    }

    // Admin verifies return => set status completed and allow admin to update asset conditions
    public static function verifyReturnByAdmin(int $bookingId, int $adminId): bool
    {
        return DB::transaction(function () use ($bookingId, $adminId) {
            $booking = static::findOrFail($bookingId);

            // set admin who verified
            $booking->id_admin = $adminId;
            $booking->status = 'completed';
            $booking->save();

            // mark assets available
            $assetIds = DB::table('booking_assets')->where('id_booking', $bookingId)->pluck('id_asset')->toArray();
            Asset::whereIn('id_asset', $assetIds)->update(['status' => 'available']);

            // recalc masters
            $masters = Asset::whereIn('id_asset', $assetIds)->select('id_master')->distinct()->pluck('id_master')->toArray();
            foreach ($masters as $m) {
                $am = AssetMaster::find($m);
                if ($am) $am->recalcStock();
            }

            // Now system should prompt admin to set asset_condition per asset (UI work)
            return true;
        });
    }
}
