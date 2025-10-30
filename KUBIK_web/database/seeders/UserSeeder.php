<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id_user' => 1,
                'name' => 'Budi',
                'email' => 'budi@student.com',
                'password' => '$2y$12$IowaDzlWbuDE7fbHW9uWTO65NfXyjAflsTHhdbBJAYsqjo9OwIcy2',
                'phone_number' => null,
                'created_at' => '2025-10-22 23:23:06',
                'updated_at' => '2025-10-22 23:23:06',
            ],
            [
                'id_user' => 2,
                'name' => 'Hafidz',
                'email' => 'Hafidz@stuudent.ac.id',
                'password' => '$2y$12$Eua1z/mOjq9f7x7etQzx8utGe92r.r7kw4LLJ0hSBpAw7Oe59RuJW',
                'phone_number' => null,
                'created_at' => '2025-10-26 05:53:00',
                'updated_at' => '2025-10-26 05:53:00',
            ],
        ]);
    }
}
