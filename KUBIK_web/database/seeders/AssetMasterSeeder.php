<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AssetMasterSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('asset_masters')->insert([
            ['id_master' => 'AM-000001', 'id_category' => 'CAT-000001', 'id_type' => 'TYP-000001', 'name' => 'AG01', 'description' => null, 'stock_total' => 2, 'stock_available' => 2, 'created_at' => '2025-10-26 12:00:52'],
            ['id_master' => 'AM-000002', 'id_category' => 'CAT-000001', 'id_type' => 'TYP-000001', 'name' => 'AG02', 'description' => null, 'stock_total' => 2, 'stock_available' => 2, 'created_at' => '2025-10-26 12:00:56'],
            ['id_master' => 'AM-000003', 'id_category' => 'CAT-000001', 'id_type' => 'TYP-000001', 'name' => 'AG03', 'description' => null, 'stock_total' => 2, 'stock_available' => 2, 'created_at' => '2025-10-26 12:01:01'],
            ['id_master' => 'AM-000004', 'id_category' => 'CAT-000001', 'id_type' => 'TYP-000001', 'name' => 'AG04', 'description' => null, 'stock_total' => 2, 'stock_available' => 2, 'created_at' => '2025-10-26 12:01:05'],
            ['id_master' => 'AM-000005', 'id_category' => 'CAT-000001', 'id_type' => 'TYP-000001', 'name' => 'AG05', 'description' => null, 'stock_total' => 2, 'stock_available' => 2, 'created_at' => '2025-10-26 12:01:09'],
            ['id_master' => 'AM-000006', 'id_category' => 'CAT-000001', 'id_type' => 'TYP-000001', 'name' => 'AG06', 'description' => null, 'stock_total' => 2, 'stock_available' => 2, 'created_at' => '2025-10-26 12:01:12'],
            ['id_master' => 'AM-000007', 'id_category' => 'CAT-000001', 'id_type' => 'TYP-000001', 'name' => 'AG07', 'description' => null, 'stock_total' => 1, 'stock_available' => 1, 'created_at' => '2025-10-26 12:04:10'],
            ['id_master' => 'AM-000008', 'id_category' => 'CAT-000001', 'id_type' => 'TYP-000001', 'name' => 'AG08', 'description' => null, 'stock_total' => 1, 'stock_available' => 1, 'created_at' => '2025-10-26 12:04:55'],
        ]);
    }
}
