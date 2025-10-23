<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Booking;
use App\Models\Asset;
use App\Models\AssetMaster;

class BookingAssetController extends Controller
{
    /**
     * Tampilkan daftar aset dalam satu booking
     */
    public function index($id_booking)
    {
        $booking = Booking::with(['assets:id_asset,id_master,status,asset_condition'])
            ->where('id_booking', $id_booking)
            ->first();

        if (!$booking) {
            return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Daftar aset dalam booking',
            'data' => $booking->assets
        ]);
    }

    /**
     * Tambahkan aset ke dalam booking
     * Smart Logic:
     * - update status aset ke 'borrowed'
     * - sinkronisasi stok di asset_master
     */
    public function attachAsset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_booking' => 'required|exists:bookings,id_booking',
            'id_asset' => 'required|exists:assets,id_asset'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking = Booking::find($request->id_booking);
        $asset = Asset::find($request->id_asset);

        if ($asset->status === 'borrowed') {
            return response()->json(['message' => 'Aset ini sedang dipinjam oleh booking lain'], 409);
        }

        DB::transaction(function () use ($booking, $asset) {
            // Hubungkan aset ke booking
            $booking->assets()->attach($asset->id_asset);

            // Update status aset
            $asset->update(['status' => 'borrowed']);

            // Update stok tersedia di asset_master
            $this->syncStock($asset->id_master);
        });

        return response()->json([
            'message' => 'Aset berhasil ditambahkan ke booking',
            'booking_id' => $booking->id_booking,
            'asset_id' => $asset->id_asset
        ]);
    }

    /**
     * Hapus aset dari booking
     * Smart Logic:
     * - ubah status aset ke 'available'
     * - sinkronisasi stok
     */
    public function detachAsset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_booking' => 'required|exists:bookings,id_booking',
            'id_asset' => 'required|exists:assets,id_asset'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking = Booking::find($request->id_booking);
        $asset = Asset::find($request->id_asset);

        if (!$booking || !$asset) {
            return response()->json(['message' => 'Data booking atau aset tidak ditemukan'], 404);
        }

        DB::transaction(function () use ($booking, $asset) {
            $booking->assets()->detach($asset->id_asset);

            // Ubah status aset kembali ke available
            $asset->update(['status' => 'available']);

            // Sinkronisasi stok
            $this->syncStock($asset->id_master);
        });

        return response()->json([
            'message' => 'Aset berhasil dihapus dari booking',
            'booking_id' => $booking->id_booking,
            'asset_id' => $asset->id_asset
        ]);
    }

    /**
     * Smart Logic: sinkronisasi stok di asset_master
     */
    private function syncStock($id_master)
    {
        $availableCount = Asset::where('id_master', $id_master)
            ->where('status', 'available')
            ->where('asset_condition', 'good')
            ->count();

        AssetMaster::where('id_master', $id_master)
            ->update(['stock_available' => $availableCount]);
    }
}
