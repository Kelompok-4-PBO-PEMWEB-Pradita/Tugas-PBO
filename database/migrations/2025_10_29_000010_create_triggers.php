<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Drop if exists to ensure idempotency in dev
        DB::unprepared('DROP TRIGGER IF EXISTS trg_assets_after_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_assets_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_assets_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_booking_approve');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_booking_completed');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_bookings_late_return');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_booking_assets_check');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_asset_master_id');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_categories_id');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_types_id');

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_assets_after_delete AFTER DELETE ON assets FOR EACH ROW BEGIN
          UPDATE asset_masters
          SET stock_total = GREATEST(stock_total - 1,0),
              stock_available = GREATEST(stock_available - IF(OLD.status='Available',1,0),0)
          WHERE id_master = OLD.id_master;
        END
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_assets_after_insert AFTER INSERT ON assets FOR EACH ROW BEGIN
          UPDATE asset_masters
          SET stock_total = stock_total + 1,
              stock_available = stock_available + IF(NEW.status='Available',1,0)
          WHERE id_master = NEW.id_master;
        END
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_assets_after_update AFTER UPDATE ON assets FOR EACH ROW BEGIN
          IF NEW.id_master = OLD.id_master THEN
            UPDATE asset_masters
            SET stock_available = stock_available
                + IF(NEW.status='Available',1,0)
                - IF(OLD.status='Available',1,0)
            WHERE id_master = NEW.id_master;
          ELSE
            UPDATE asset_masters
            SET stock_total = stock_total - 1,
                stock_available = stock_available - IF(OLD.status='Available',1,0)
            WHERE id_master = OLD.id_master;

            UPDATE asset_masters
            SET stock_total = stock_total + 1,
                stock_available = stock_available + IF(NEW.status='Available',1,0)
            WHERE id_master = NEW.id_master;
          END IF;
        END
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_booking_approve AFTER UPDATE ON bookings FOR EACH ROW BEGIN
          IF NEW.status = 'Approved' AND OLD.status <> 'Approved' THEN
            UPDATE assets a
            JOIN booking_assets ba ON a.id_asset = ba.id_asset
              AND ba.id_booking = NEW.id_booking
            SET a.status = 'Borrowed';
          END IF;
        END
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_booking_completed AFTER UPDATE ON bookings FOR EACH ROW BEGIN
          IF NEW.status = 'Completed' AND OLD.status <> 'Completed' THEN
            UPDATE assets a
            JOIN booking_assets ba ON a.id_asset = ba.id_asset
              AND ba.id_booking = NEW.id_booking
            SET a.status = 'Available';
          END IF;
        END
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_bookings_late_return BEFORE UPDATE ON bookings FOR EACH ROW BEGIN
          IF NEW.return_at IS NOT NULL THEN
            SET NEW.late_return = GREATEST(TIMESTAMPDIFF(HOUR, NEW.end_time, NEW.return_at), 0);
          END IF;
        END
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_booking_assets_check BEFORE INSERT ON booking_assets FOR EACH ROW BEGIN
          DECLARE asset_status VARCHAR(20);
          SELECT status INTO asset_status FROM assets WHERE id_asset = NEW.id_asset;
          IF asset_status <> 'Available' THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Asset is currently not available for booking';
          END IF;
        END
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_asset_master_id BEFORE INSERT ON asset_masters FOR EACH ROW BEGIN
          DECLARE newId INT;
          SET newId = (SELECT COUNT(*) + 1 FROM asset_masters);
          SET NEW.id_master = CONCAT('AM-', LPAD(newId, 6, '0'));
        END
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_categories_id BEFORE INSERT ON categories FOR EACH ROW BEGIN
          DECLARE newId INT;
          SET newId = (SELECT COUNT(*) + 1 FROM categories);
          SET NEW.id_category = CONCAT('CAT-', LPAD(newId, 6, '0'));
        END
        SQL);

        DB::unprepared(<<<'SQL'
        CREATE TRIGGER trg_types_id BEFORE INSERT ON types FOR EACH ROW BEGIN
          DECLARE newId INT;
          SET newId = (SELECT COUNT(*) + 1 FROM types);
          SET NEW.id_type = CONCAT('TYP-', LPAD(newId, 6, '0'));
        END
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_assets_after_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_assets_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_assets_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_booking_approve');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_booking_completed');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_bookings_late_return');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_booking_assets_check');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_asset_master_id');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_categories_id');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_types_id');
    }
};
