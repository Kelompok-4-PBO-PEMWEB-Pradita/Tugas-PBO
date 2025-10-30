<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('notifications')->insert([
            ['id_notif' => 1, 'id_user' => 1, 'message' => 'Peminjaman kamu telah disetujui untuk booking ID: 7', 'is_read' => 0, 'created_at' => '2025-10-26 01:42:29'],
            ['id_notif' => 2, 'id_user' => 1, 'message' => 'Pengembalian kamu telah diverifikasi untuk booking ID: 7', 'is_read' => 0, 'created_at' => '2025-10-26 01:47:17'],
            ['id_notif' => 3, 'id_user' => 1, 'message' => 'Peminjaman kamu ditolak untuk booking ID: 8', 'is_read' => 0, 'created_at' => '2025-10-26 01:51:34'],
            ['id_notif' => 4, 'id_user' => 1, 'message' => 'Peminjaman kamu ditolak untuk booking ID: 8', 'is_read' => 0, 'created_at' => '2025-10-26 01:52:01'],
            ['id_notif' => 5, 'id_user' => 1, 'message' => 'Peminjaman kamu telah disetujui untuk booking ID: 9', 'is_read' => 0, 'created_at' => '2025-10-26 04:55:11'],
            ['id_notif' => 6, 'id_user' => 1, 'message' => 'Pengembalian kamu telah diverifikasi untuk booking ID: 9', 'is_read' => 0, 'created_at' => '2025-10-26 04:57:50'],
            ['id_notif' => 7, 'id_user' => 2, 'message' => 'Peminjaman kamu telah disetujui untuk booking ID: 11', 'is_read' => 0, 'created_at' => '2025-10-26 06:04:10'],
            ['id_notif' => 8, 'id_user' => 2, 'message' => 'Pengembalian kamu telah diverifikasi untuk booking ID: 11', 'is_read' => 0, 'created_at' => '2025-10-26 06:05:25'],
            ['id_notif' => 9, 'id_user' => 2, 'message' => 'Peminjaman kamu telah disetujui untuk booking ID: 12', 'is_read' => 0, 'created_at' => '2025-10-28 21:31:13'],
            ['id_notif' => 10, 'id_user' => 2, 'message' => 'Pengembalian kamu telah diverifikasi untuk booking ID: 12', 'is_read' => 0, 'created_at' => '2025-10-28 21:32:18'],
        ]);
    }
}
