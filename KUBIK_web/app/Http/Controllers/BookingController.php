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
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Tampilkan semua data booking (untuk admin)
     */
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

    /**
     * User membuat permintaan peminjaman
     * Smart Logic: status otomatis 'pending'
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required|exists:users,id_user',
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
            // Buat booking baru
            $booking = Booking::create([
                'id_user' => $request->id_user,
                'status' => 'pending',
                'start_time' => $request->start_time,
                'end_time' => $request->end_time
            ]);

            // Hubungkan aset yang dipinjam
            foreach ($request->assets as $assetId) {
                $booking->assets()->attach($assetId);
            }

            // Notifikasi ke admin
            Notification::create([
                'id_admin' => Admin::inRandomOrder()->first()->id_admin,
                'message' => 'Permintaan peminjaman baru dari user ID: ' . $request->id_user
            ]);

            DB::commit();
            return response()->json(['message' => 'Permintaan peminjaman berhasil dikirim', 'data' => $booking], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal membuat booking', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Admin menyetujui permintaan
     * Smart Logic:
     * - update status aset menjadi borrowed
     * - ubah status booking ke approved
     */
    public function approve($id)
    {
        $booking = Booking::with('assets')->find($id);

        if (!$booking) return response()->json(['message' => 'Booking tidak ditemukan'], 404);
        if ($booking->status !== 'pending') return response()->json(['message' => 'Hanya booking dengan status pending yang dapat disetujui'], 400);

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'approved']);

            foreach ($booking->assets as $asset) {
                $asset->update(['status' => 'borrowed']);
            }

            Notification::create([
                'id_admin' => Admin::inRandomOrder()->first()->id_admin,
                'message' => 'Peminjaman disetujui untuk booking ID: ' . $booking->id_booking
            ]);
        });

        return response()->json(['message' => 'Booking disetujui dan aset ditandai sebagai dipinjam']);
    }

    /**
     * Admin menolak permintaan
     */
    public function reject($id)
    {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['message' => 'Booking tidak ditemukan'], 404);

        $booking->update(['status' => 'rejected']);

        Notification::create([
            'id_admin' => Admin::inRandomOrder()->first()->id_admin,
            'message' => 'Booking ID ' . $id . ' ditolak'
        ]);

        return response()->json(['message' => 'Booking ditolak']);
    }

    /**
     * User mengajukan pengembalian
     * Smart Logic:
     * - status otomatis "pending" (menunggu verif admin)
     * - sistem menghitung keterlambatan dalam jam
     */
    public function requestReturn($id)
    {
        $booking = Booking::with('assets')->find($id);
        if (!$booking) return response()->json(['message' => 'Booking tidak ditemukan'], 404);

        $returnTime = Carbon::now();
        $endTime = Carbon::parse($booking->end_time);
        $lateHours = $returnTime->greaterThan($endTime)
            ? $endTime->diffInHours($returnTime)
            : 0;

        $booking->update([
            'status' => 'pending', // menunggu verifikasi pengembalian
            'return_at' => $returnTime,
            'late_return' => $lateHours
        ]);

        Notification::create([
            'id_admin' => Admin::inRandomOrder()->first()->id_admin,
            'message' => 'User mengajukan pengembalian untuk booking ID: ' . $id
        ]);

        return response()->json([
            'message' => 'Permintaan pengembalian dikirim, menunggu verifikasi admin',
            'late_return' => $lateHours . ' jam'
        ]);
    }

    /**
     * Admin memverifikasi pengembalian
     * Smart Logic:
     * - update status ke completed
     * - ubah aset ke available
     * - kirim notifikasi & update stok otomatis
     */
    public function confirmReturn($id)
    {
        $booking = Booking::with('assets')->find($id);
        if (!$booking) return response()->json(['message' => 'Booking tidak ditemukan'], 404);

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'completed']);

            foreach ($booking->assets as $asset) {
                $asset->update(['status' => 'available']);
            }

            // sinkronisasi stok tersedia
            foreach ($booking->assets as $asset) {
                $availableCount = Asset::where('id_master', $asset->id_master)
                    ->where('status', 'available')
                    ->where('asset_condition', 'good')
                    ->count();
                AssetMaster::where('id_master', $asset->id_master)
                    ->update(['stock_available' => $availableCount]);
            }

            Notification::create([
                'id_admin' => Admin::inRandomOrder()->first()->id_admin,
                'message' => 'Pengembalian disetujui untuk booking ID: ' . $booking->id_booking
            ]);
        });

        return response()->json(['message' => 'Pengembalian diverifikasi dan stok diperbarui']);
    }
}
