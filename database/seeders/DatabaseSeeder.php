<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Order matters for FK integrity
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            TypeSeeder::class,
            AssetMasterSeeder::class,
            AssetSeeder::class,
            BookingSeeder::class,
            BookingAssetSeeder::class,
            NotificationSeeder::class,
            AdminNotificationSeeder::class,
        ]);
    }
}
