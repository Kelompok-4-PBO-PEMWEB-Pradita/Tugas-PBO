<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['id_category' => 'CAT-000001', 'name' => 'Ruang Kelas', 'created_at' => '2025-10-23 04:32:46'],
            ['id_category' => 'CAT-000002', 'name' => 'Kursi', 'created_at' => '2025-10-27 02:40:36'],
        ]);
    }
}
