<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class BookingAssetSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('booking_assets')->insert([
            ['id_booking' => 10, 'id_asset' => 16, 'created_at' => '2025-10-26 05:57:36'],
            ['id_booking' => 11, 'id_asset' => 16, 'created_at' => '2025-10-26 05:58:09'],
            ['id_booking' => 12, 'id_asset' => 14, 'created_at' => '2025-10-28 21:29:58'],
        ]);
    }
}
