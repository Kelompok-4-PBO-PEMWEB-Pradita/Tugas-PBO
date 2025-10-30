<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admins')->insert([
            [
                'id_admin' => 1,
                'name' => 'Anton',
                'email' => 'anton@admin.com',
                'password' => '$2y$12$TfGlP0H70KKSnXEmakT/BummMRzi94R25uQ5ePg3JYpkFSK3aoMLW',
                'created_at' => '2025-10-22 23:29:28',
                'updated_at' => '2025-10-22 23:29:28',
            ],
            [
                'id_admin' => 2,
                'name' => 'Admin',
                'email' => 'admin@pradita.ac.id',
                'password' => '$2y$12$iGas3CSN3ponHgwde9Nwmul/tpmBk2QKeDzdFmbgm9rYLtX.Jeylm',
                'created_at' => '2025-10-26 06:00:33',
                'updated_at' => '2025-10-26 06:00:33',
            ]
        ]);
    }
}
