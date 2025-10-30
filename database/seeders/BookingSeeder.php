<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class BookingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bookings')->insert([
            ['id_booking' => 7, 'id_user' => 1, 'id_admin' => null, 'start_time' => '2025-10-25 09:00:00', 'end_time' => '2025-10-25 13:00:00', 'return_at' => '2025-10-26 08:46:35', 'late_return' => 19, 'status' => 'Completed', 'created_at' => '2025-10-26 01:40:15', 'updated_at' => '2025-10-26 01:47:17'],
            ['id_booking' => 8, 'id_user' => 1, 'id_admin' => null, 'start_time' => '2025-10-25 09:00:00', 'end_time' => '2025-10-25 13:00:00', 'return_at' => null, 'late_return' => 0, 'status' => 'Rejected', 'created_at' => '2025-10-26 01:51:03', 'updated_at' => '2025-10-26 01:51:34'],
            ['id_booking' => 9, 'id_user' => 1, 'id_admin' => 1,    'start_time' => '2025-10-25 09:00:00', 'end_time' => '2025-10-25 13:00:00', 'return_at' => '2025-10-26 11:57:09', 'late_return' => 22, 'status' => 'Completed', 'created_at' => '2025-10-26 04:46:36', 'updated_at' => '2025-10-26 04:57:50'],
            ['id_booking' => 10,'id_user' => 2, 'id_admin' => null, 'start_time' => '2025-10-25 09:00:00', 'end_time' => '2025-10-25 13:00:00', 'return_at' => null, 'late_return' => 0, 'status' => 'Pending', 'created_at' => '2025-10-26 05:57:36', 'updated_at' => '2025-10-26 05:57:36'],
            ['id_booking' => 11,'id_user' => 2, 'id_admin' => 2,    'start_time' => '2025-10-25 09:00:00', 'end_time' => '2025-10-25 13:00:00', 'return_at' => '2025-10-26 13:04:57', 'late_return' => 24, 'status' => 'Completed', 'created_at' => '2025-10-26 05:58:09', 'updated_at' => '2025-10-26 06:05:25'],
            ['id_booking' => 12,'id_user' => 2, 'id_admin' => 1,    'start_time' => '2025-10-25 09:00:00', 'end_time' => '2025-10-25 13:00:00', 'return_at' => '2025-10-29 04:31:57', 'late_return' => 87, 'status' => 'Completed', 'created_at' => '2025-10-28 21:29:58', 'updated_at' => '2025-10-28 21:32:18'],
        ]);
    }
}
