<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class TypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('types')->insert([
            ['id_type' => 'TYP-000001', 'name' => 'Ruangan', 'description' => null, 'created_at' => '2025-10-23 04:32:37'],
            ['id_type' => 'TYP-000002', 'name' => 'Barang', 'description' => null, 'created_at' => '2025-10-27 02:46:45'],
        ]);
    }
}
