<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AssetSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('assets')->insert([
            ['id_asset' => 3, 'id_master' => 'AM-000001', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:00:52', 'updated_at' => '2025-10-26 12:00:52'],
            ['id_asset' => 4, 'id_master' => 'AM-000001', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:00:52', 'updated_at' => '2025-10-26 12:00:52'],
            ['id_asset' => 5, 'id_master' => 'AM-000002', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:00:56', 'updated_at' => '2025-10-27 03:00:16'],
            ['id_asset' => 6, 'id_master' => 'AM-000002', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:00:56', 'updated_at' => '2025-10-26 12:00:56'],
            ['id_asset' => 7, 'id_master' => 'AM-000003', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:01:01', 'updated_at' => '2025-10-26 12:01:01'],
            ['id_asset' => 8, 'id_master' => 'AM-000003', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:01:01', 'updated_at' => '2025-10-26 12:01:01'],
            ['id_asset' => 9, 'id_master' => 'AM-000004', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:01:05', 'updated_at' => '2025-10-26 12:01:05'],
            ['id_asset' => 10,'id_master' => 'AM-000004', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:01:05', 'updated_at' => '2025-10-26 12:01:05'],
            ['id_asset' => 11,'id_master' => 'AM-000005', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:01:09', 'updated_at' => '2025-10-26 12:01:09'],
            ['id_asset' => 12,'id_master' => 'AM-000005', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:01:09', 'updated_at' => '2025-10-26 12:01:09'],
            ['id_asset' => 13,'id_master' => 'AM-000006', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:01:12', 'updated_at' => '2025-10-26 12:01:12'],
            ['id_asset' => 14,'id_master' => 'AM-000006', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:01:12', 'updated_at' => '2025-10-29 04:32:18'],
            ['id_asset' => 15,'id_master' => 'AM-000007', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:04:10', 'updated_at' => '2025-10-26 12:04:10'],
            ['id_asset' => 16,'id_master' => 'AM-000008', 'asset_condition' => 'Good', 'status' => 'Available', 'created_at' => '2025-10-26 12:04:55', 'updated_at' => '2025-10-26 13:05:25'],
        ]);
    }
}
