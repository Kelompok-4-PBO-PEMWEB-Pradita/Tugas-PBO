<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Booking;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function index() {
        return Admin::orderByDesc('created_at')->get();
    }
    public function store(Request $r) {
        $r->validate(['name'=>'required','email'=>'required|email|unique:admins,email','password'=>'required|min:6']);
        $admin = Admin::create(['name'=>$r->name,'email'=>$r->email,'password'=>bcrypt($r->password)]);
        return response()->json(['message'=>'Admin created','data'=>$admin],201);
    }
    public function show($id){ return Admin::findOrFail($id); }
    public function update(Request $r,$id){ $admin = Admin::findOrFail($id); if($r->has('name')) $admin->name=$r->name; if($r->has('email')) $admin->email=$r->email; $admin->save(); return response()->json(['message'=>'Updated','data'=>$admin]); }
    public function destroy($id){ Admin::findOrFail($id)->delete(); return response()->json(['message'=>'Deleted']); }

    // Export bookings as CSV. Filter by date_from, date_to, status optionally
    public function exportBookingsCsv(Request $req)
    {
        $dateFrom = $req->query('date_from'); // yyyy-mm-dd
        $dateTo = $req->query('date_to');
        $status = $req->query('status');

        $query = Booking::with('user','assets','admin')->orderBy('created_at','desc');

        if ($dateFrom) $query->whereDate('created_at','>=',$dateFrom);
        if ($dateTo) $query->whereDate('created_at','<=',$dateTo);
        if ($status) $query->where('status',$status);

        $filename = 'bookings_export_'.date('Ymd_His').'.csv';
        $response = new StreamedResponse(function() use ($query) {
            $handle = fopen('php://output', 'w');
            // header
            fputcsv($handle, ['booking_id','user_email','assets','start_time','end_time','status','created_at']);
            $query->chunk(500, function($rows) use ($handle) {
                foreach ($rows as $b) {
                    $assetList = $b->assets->pluck('id_asset')->implode('|');
                    fputcsv($handle, [
                        $b->id_booking,
                        $b->user->email ?? '',
                        $assetList,
                        $b->start_time,
                        $b->end_time,
                        $b->status,
                        $b->created_at
                    ]);
                }
            });
            fclose($handle);
        });

        $response->headers->set('Content-Type','text/csv');
        $response->headers->set('Content-Disposition',"attachment; filename={$filename}");
        return $response;
    }
}
