<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\BookingAsset;
use App\Models\Asset;
use App\Models\AssetMaster;
use App\Models\Notification;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Create booking (user) with multiple assets
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_user' => 'required|integer|exists:users,id_user',
            'assets' => 'required|array|min:1',
            'assets.*' => 'integer|exists:assets,id_asset',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'note' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Check availability of each asset
            $assets = Asset::whereIn('id_asset', $validated['assets'])->lockForUpdate()->get();
            if (count($assets) !== count($validated['assets'])) {
                return response()->json(['error'=>'Some assets were not found'], 404);
            }
            foreach ($assets as $asset) {
                if ($asset->status !== 'available') {
                    return response()->json(['error'=>"Asset id {$asset->id_asset} is not available"], 400);
                }
            }

            // Create booking
            $booking = Booking::create([
                'id_user' => $validated['id_user'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'note' => $validated['note'] ?? null,
                'status' => 'Pending Approval'
            ]);

            // Create pivot booking_assets
            foreach ($assets as $asset) {
                BookingAsset::create([
                    'id_booking' => $booking->id_booking,
                    'id_asset' => $asset->id_asset
                ]);
            }

            // Notify user (and optionally admin) — here we notify user
            Notification::create([
                'id_user' => $validated['id_user'],
                'message' => 'Permintaan peminjaman terkirim. Menunggu approval.'
            ]);

            DB::commit();
            return response()->json(['message'=>'Booking created','data'=>$booking],201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error'=>'Failed to create booking','message'=>$e->getMessage()],500);
        }
    }

    // Cancel booking (user) if not approved/borrowed yet
    public function cancel($id)
    {
        $booking = Booking::with('assets')->findOrFail($id);

        if (in_array($booking->status, ['Borrowed','Returned'])) {
            return response()->json(['error'=>'Cannot cancel a booking that is already borrowed or returned'],400);
        }

        // delete pivot then booking
        DB::transaction(function() use ($booking) {
            foreach ($booking->assets as $asset) {
                BookingAsset::where('id_booking', $booking->id_booking)->where('id_asset',$asset->id_asset)->delete();
            }
            $booking->delete();
        });

        return response()->json(['message'=>'Booking cancelled']);
    }

    // Approve booking (admin)
    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $booking = Booking::with('assets')->findOrFail($id);

            if ($booking->status !== 'Pending Approval') {
                return response()->json(['error'=>'Booking not pending approval'],400);
            }

            // Double-check assets are still available
            foreach ($booking->assets as $asset) {
                $assetReload = Asset::find($asset->id_asset);
                if ($assetReload->status !== 'available') {
                    return response()->json(['error'=>"Asset id {$assetReload->id_asset} not available"],400);
                }
            }

            // Mark booking approved
            $booking->status = 'Approved';
            $booking->save();

            // Do not change asset status to borrowed until actual pickup; but optionally we can mark as reserved.
            // For simplicity, keep status 'available' until markBorrowed is called. We decrement stock_available here as reservation.
            foreach ($booking->assets as $asset) {
                $master = AssetMaster::where('id_master', $asset->id_master)->first();
                if ($master) {
                    $master->stock_available = max(0, $master->stock_available - 1);
                    $master->save();
                }
            }

            Notification::create([
                'id_user' => $booking->id_user,
                'message' => 'Peminjaman Anda telah disetujui oleh admin.'
            ]);

            DB::commit();
            return response()->json(['message'=>'Booking approved']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error'=>'Failed to approve booking','message'=>$e->getMessage()],500);
        }
    }

    // Reject booking (admin)
    public function reject($id)
    {
        $booking = Booking::with('assets')->findOrFail($id);
        if ($booking->status !== 'Pending Approval') {
            return response()->json(['error'=>'Booking status not pending'],400);
        }
        $booking->status = 'Rejected';
        $booking->save();

        Notification::create([
            'id_user' => $booking->id_user,
            'message' => 'Peminjaman Anda ditolak oleh admin.'
        ]);

        return response()->json(['message'=>'Booking rejected']);
    }

    // Mark booking as borrowed (when user picks up items) -> set actual_start and change each asset status to 'borrowed'
    public function markBorrowed($id)
    {
        DB::beginTransaction();
        try {
            $booking = Booking::with('assets')->findOrFail($id);

            if ($booking->status !== 'Approved') {
                return response()->json(['error'=>'Booking must be Approved to mark as Borrowed'],400);
            }

            $booking->actual_start = Carbon::now();
            $booking->status = 'Borrowed';
            $booking->save();

            foreach ($booking->assets as $asset) {
                $assetModel = Asset::find($asset->id_asset);
                $assetModel->status = 'borrowed';
                $assetModel->save();
            }

            Notification::create([
                'id_user' => $booking->id_user,
                'message' => 'Anda telah mengambil barang. Selamat menggunakan.'
            ]);

            DB::commit();
            return response()->json(['message'=>'Booking marked as borrowed']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error'=>'Failed to mark borrowed','message'=>$e->getMessage()],500);
        }
    }

    // Return booking: either return all assets or subset. If subset provided, we consider partial return (still keep booking Borrowed until all returned)
    public function returnBooking(Request $request, $id)
    {
        $validated = $request->validate([
            'returned_assets' => 'nullable|array',
            'returned_assets.*' => 'integer|exists:assets,id_asset',
            'note' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $booking = Booking::with('assets')->findOrFail($id);

            if ($booking->status !== 'Borrowed' && $booking->status !== 'Approved') {
                return response()->json(['error'=>'Booking not in Borrowed or Approved state'],400);
            }

            $returnAll = !isset($validated['returned_assets']) || empty($validated['returned_assets']);
            $assetsToReturn = $booking->assets->filter(function($a) use ($validated, $returnAll) {
                if ($returnAll) return true;
                return in_array($a->id_asset, $validated['returned_assets']);
            });

            if ($assetsToReturn->isEmpty()) {
                return response()->json(['error'=>'No assets to return'],400);
            }

            // Set booking return_at/time once (the booking-level history)
            $booking->return_at = Carbon::now();
            // compute late_return (hours) based on booking.end_time
            $end = Carbon::parse($booking->end_time);
            $now = Carbon::parse($booking->return_at);
            $booking->late_return = $now->greaterThan($end) ? $end->diffInHours($now) : 0;
            $booking->note = $validated['note'] ?? $booking->note;

            // Update asset status and master stock_available
            foreach ($assetsToReturn as $assetPivot) {
                $assetModel = Asset::find($assetPivot->id_asset);
                $assetModel->status = 'available';
                $assetModel->save();

                // increment stock_available
                $master = AssetMaster::where('id_master', $assetModel->id_master)->first();
                if ($master) {
                    $master->stock_available += 1;
                    $master->save();
                }

                // remove pivot relation (optional keep for history — but we keep pivot for record)
            }

            // If all assets returned -> mark booking Returned; else Partial -> keep Borrowed or set 'Partially Returned'
            $remainingBorrowed = $booking->assets()->where('status','borrowed')->count();
            if ($remainingBorrowed === 0) {
                $booking->status = 'Returned';
            } else {
                $booking->status = 'Partially Returned';
            }

            $booking->save();

            Notification::create([
                'id_user' => $booking->id_user,
                'message' => 'Barang dikembalikan. Terima kasih.'
            ]);

            DB::commit();
            return response()->json(['message'=>'Return processed', 'booking_status' => $booking->status]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error'=>'Failed to process return','message'=>$e->getMessage()],500);
        }
    }
}
