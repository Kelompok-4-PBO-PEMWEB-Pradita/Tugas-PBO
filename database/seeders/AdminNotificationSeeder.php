<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AdminNotificationSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admin_notifications')->insert([
            ['id_notif_admin' => 1, 'id_admin' => 1, 'message' => 'Permintaan peminjaman baru dari user ID: 1', 'is_read' => 0, 'created_at' => '2025-10-26 01:40:15'],
            ['id_notif_admin' => 2, 'id_admin' => 1, 'message' => 'User mengajukan pengembalian untuk booking ID: 7', 'is_read' => 0, 'created_at' => '2025-10-26 01:46:35'],
            ['id_notif_admin' => 3, 'id_admin' => 1, 'message' => 'Permintaan peminjaman baru dari user ID: 1', 'is_read' => 0, 'created_at' => '2025-10-26 01:51:03'],
            ['id_notif_admin' => 4, 'id_admin' => 1, 'message' => 'Permintaan peminjaman baru dari user ID: 1', 'is_read' => 0, 'created_at' => '2025-10-26 04:46:39'],
            ['id_notif_admin' => 5, 'id_admin' => 1, 'message' => 'User mengajukan pengembalian untuk booking ID: 9', 'is_read' => 0, 'created_at' => '2025-10-26 04:57:09'],
            ['id_notif_admin' => 6, 'id_admin' => 1, 'message' => 'Permintaan peminjaman baru dari user ID: 2', 'is_read' => 0, 'created_at' => '2025-10-26 05:57:36'],
            ['id_notif_admin' => 7, 'id_admin' => 1, 'message' => 'Permintaan peminjaman baru dari user ID: 2', 'is_read' => 0, 'created_at' => '2025-10-26 05:58:09'],
            ['id_notif_admin' => 8, 'id_admin' => 1, 'message' => 'User mengajukan pengembalian untuk booking ID: 11', 'is_read' => 0, 'created_at' => '2025-10-26 06:04:57'],
            ['id_notif_admin' => 9, 'id_admin' => 2, 'message' => 'User mengajukan pengembalian untuk booking ID: 11', 'is_read' => 0, 'created_at' => '2025-10-26 06:04:57'],
            ['id_notif_admin' => 10, 'id_admin' => 1, 'message' => 'Permintaan peminjaman baru dari user ID: 2', 'is_read' => 0, 'created_at' => '2025-10-28 21:29:59'],
            ['id_notif_admin' => 11, 'id_admin' => 2, 'message' => 'Permintaan peminjaman baru dari user ID: 2', 'is_read' => 0, 'created_at' => '2025-10-28 21:29:59'],
            ['id_notif_admin' => 12, 'id_admin' => 1, 'message' => 'User mengajukan pengembalian untuk booking ID: 12', 'is_read' => 0, 'created_at' => '2025-10-28 21:31:57'],
            ['id_notif_admin' => 13, 'id_admin' => 2, 'message' => 'User mengajukan pengembalian untuk booking ID: 12', 'is_read' => 0, 'created_at' => '2025-10-28 21:31:57'],
        ]);
    }
}
