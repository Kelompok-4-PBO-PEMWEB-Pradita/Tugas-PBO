# Laravel Database (kubik)

This folder contains migrations, triggers, and seeders converted from your SQL dump.
Tested with Laravel 10+ and MySQL/MariaDB.

## Setup

1) Copy the `database/` folder into your Laravel project root (it will merge with your project's `database/`).
2) Ensure your `.env` DB connection points to an empty database.
3) Run:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

## Notes
- Triggers are created in a dedicated migration: `create_triggers.php`.
- The `booking_assets` insert check trigger uses `'Available'` (capital A) to match the ENUM values.
- ID generators for `asset_masters`, `categories`, and `types` are implemented as DB triggers to match your dump. Consider replacing with an application-level generator if you need stricter sequencing.
- Seeders preserve original IDs and timestamps.
