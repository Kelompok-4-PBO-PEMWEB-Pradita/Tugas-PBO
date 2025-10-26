<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Asset;
use App\Models\AssetMaster;
use App\Models\Admin;
use App\Models\User;
use App\Models\Notification;
use App\Models\AdminNotification;
use Carbon\Carbon;

class BookingController extends Controller
{
    /** User melihat semua booking yang dia buat */
public function userBookings($id_user)
{
    $bookings = Booking::with(['assets:id_asset,id_master,status'])
        ->where('id_user', $id_user)
        ->orderBy('created_at', 'desc')
        ->get();

    if ($bookings->isEmpty()) {
        return response()->json([
            'message' => 'Belum ada peminjaman yang dibuat oleh anda'
        ]);
    }

    return response()->json([
        'message' => 'Daftar semua peminjaman milik anda',
        'data' => $bookings
    ]);
}

    /** Tampilkan semua data booking (untuk admin) */
    public function index()
    {
        $bookings = Booking::with(['user:id_user,name', 'assets:id_asset,id_master,status'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Daftar semua data peminjaman',
            'data' => $bookings
        ]);
    }

    /** User membuat permintaan peminjaman */
    public function store(Request $request, $id_user)
    {
        $validator = Validator::make($request->all(), [
            'assets' => 'required|array|min:1',
            'assets.*' => 'exists:assets,id_asset',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $booking = Booking::create([
                'id_user' => $id_user,
                'status' => 'pending',
                'start_time' => $request->start_time,
                'end_time' => $request->end_time
            ]);

            foreach ($request->assets as $assetId) {
                $booking->assets()->attach($assetId);
            }

            $admins = Admin::all();
            foreach ($admins as $admin) {
                AdminNotification::create([
                    'id_admin' => $admin->id_admin,
                    'message' => 'Permintaan peminjaman baru dari user ID: ' . $id_user,
                    'is_read' => false,
                    'created_at' => now()
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Permintaan peminjaman berhasil dikirim',
                'data' => $booking
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal membuat booking',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /** Admin menyetujui permintaan */
    public function approve($id_booking, $id_admin)
    {
        $booking = Booking::with('assets')->find($id_booking);

        if (!$booking) return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        if ($booking->status !== 'pending')
            return response()->json(['message' => 'Hanya booking dengan status pending yang dapat disetujui'], 400);

        DB::transaction(function () use ($booking, $id_admin) {
            $booking->update([
                'status' => 'approved',
                'id_admin' => $id_admin
            ]);

            foreach ($booking->assets as $asset) {
                $asset->update(['status' => 'borrowed']);
            }

            Notification::create([
                'id_user' => $booking->id_user,
                'message' => 'Peminjaman kamu telah disetujui untuk booking ID: ' . $booking->id_booking,
                'is_read' => false,
                'created_at' => now()
            ]);
        });

        return response()->json(['message' => 'Booking disetujui oleh admin ID: ' . $id_admin]);
    }

    /** Admin menolak permintaan */
    public function reject($id_booking, $id_admin)
    {
        $booking = Booking::find($id_booking);
        if (!$booking) return response()->json(['message' => 'Booking tidak ditemukan'], 404);

        $booking->update([
            'status' => 'rejected',
            'id_admin' => $id_admin
        ]);

        Notification::create([
            'id_user' => $booking->id_user,
            'message' => 'Peminjaman kamu ditolak untuk booking ID: ' . $id_booking,
            'is_read' => false,
            'created_at' => now()
        ]);

        return response()->json(['message' => 'Booking ditolak oleh admin ID: ' . $id_admin]);
    }

    /** User mengajukan pengembalian */
    public function requestReturn($id_booking)
    {
        $booking = Booking::with('assets')->find($id_booking);
        if (!$booking) return response()->json(['message' => 'Booking tidak ditemukan'], 404);

        $returnTime = Carbon::now();
        $endTime = Carbon::parse($booking->end_time);
        $lateHours = $returnTime->greaterThan($endTime)
            ? $endTime->diffInHours($returnTime)
            : 0;

        $booking->update([
            'status' => 'pending',
            'return_at' => $returnTime,
            'late_return' => $lateHours
        ]);

        $admins = Admin::all();
        foreach ($admins as $admin) {
            AdminNotification::create([
                'id_admin' => $admin->id_admin,
                'message' => 'User mengajukan pengembalian untuk booking ID: ' . $id_booking,
                'is_read' => false,
                'created_at' => now()
            ]);
        }

        return response()->json([
            'message' => 'Permintaan pengembalian dikirim, menunggu verifikasi admin',
            'late_return' => $lateHours . ' jam'
        ]);
    }

    /** Admin memverifikasi pengembalian */
    public function confirmReturn($id_booking, $id_admin)
    {
        $booking = Booking::with('assets')->find($id_booking);
        if (!$booking) return response()->json(['message' => 'Booking tidak ditemukan'], 404);

        DB::transaction(function () use ($booking, $id_admin) {
            $booking->update([
                'status' => 'completed',
                'id_admin' => $id_admin
            ]);

            foreach ($booking->assets as $asset) {
                $asset->update(['status' => 'available']);
            }

            foreach ($booking->assets as $asset) {
                $availableCount = Asset::where('id_master', $asset->id_master)
                    ->where('status', 'available')
                    ->where('asset_condition', 'good')
                    ->count();
                AssetMaster::where('id_master', $asset->id_master)
                    ->update(['stock_available' => $availableCount]);
            }

            Notification::create([
                'id_user' => $booking->id_user,
                'message' => 'Pengembalian kamu telah diverifikasi untuk booking ID: ' . $booking->id_booking,
                'is_read' => false,
                'created_at' => now()
            ]);
        });

        return response()->json(['message' => 'Pengembalian diverifikasi oleh admin ID: ' . $id_admin]);
    }
}
