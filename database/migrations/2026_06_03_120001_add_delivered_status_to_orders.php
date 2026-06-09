<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pending', 'accepted', 'in_progress', 'completed', 'delivered', 'rejected', 'cancelled'
            ) NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'sqlite') {
            DB::statement("PRAGMA foreign_keys = OFF");
            
            DB::statement("CREATE TABLE orders_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                reference VARCHAR NOT NULL,
                user_id INTEGER NOT NULL,
                printer_id INTEGER DEFAULT NULL,
                service_type VARCHAR CHECK(service_type IN ('print', 'photocopy', 'scan')) NOT NULL,
                format VARCHAR DEFAULT 'A4' CHECK(format IN ('A4', 'A3', 'A5', 'letter')) NOT NULL,
                color_mode VARCHAR DEFAULT 'bw' CHECK(color_mode IN ('color', 'bw')) NOT NULL,
                quantity INTEGER UNSIGNED DEFAULT 1 NOT NULL,
                pages INTEGER UNSIGNED DEFAULT 1 NOT NULL,
                unit_price DECIMAL(10, 2) NOT NULL,
                total_price DECIMAL(10, 2) NOT NULL,
                status VARCHAR DEFAULT 'pending' CHECK(status IN ('pending', 'accepted', 'in_progress', 'completed', 'delivered', 'rejected', 'cancelled')) NOT NULL,
                payment_method VARCHAR DEFAULT 'on_delivery' CHECK(payment_method IN ('online', 'on_delivery')) NOT NULL,
                payment_status VARCHAR DEFAULT 'unpaid' CHECK(payment_status IN ('unpaid', 'pending', 'paid', 'refunded')) NOT NULL,
                file_path VARCHAR DEFAULT NULL,
                file_name VARCHAR DEFAULT NULL,
                file_type VARCHAR DEFAULT NULL,
                file_size INTEGER UNSIGNED DEFAULT NULL,
                notes TEXT DEFAULT NULL,
                rejection_reason TEXT DEFAULT NULL,
                completed_at DATETIME DEFAULT NULL,
                created_at DATETIME DEFAULT NULL,
                updated_at DATETIME DEFAULT NULL,
                accepted_at DATETIME DEFAULT NULL
            )");
            
            DB::statement("INSERT INTO orders_new SELECT * FROM orders");
            
            DB::statement("DROP TABLE orders");
            
            DB::statement("ALTER TABLE orders_new RENAME TO orders");
            
            DB::statement("CREATE INDEX orders_status_created_at_index ON orders (status, created_at)");
            DB::statement("CREATE INDEX orders_user_id_status_index ON orders (user_id, status)");
            DB::statement("CREATE INDEX orders_printer_id_status_index ON orders (printer_id, status)");
            DB::statement("CREATE INDEX orders_status_printer_id_index ON orders (status, printer_id)");
            DB::statement("CREATE UNIQUE INDEX orders_reference_unique ON orders (reference)");
            
            DB::statement("PRAGMA foreign_keys = ON");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::table('orders')->where('status', 'delivered')->update(['status' => 'completed']);
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pending', 'accepted', 'in_progress', 'completed', 'rejected', 'cancelled'
            ) NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'sqlite') {
            DB::table('orders')->where('status', 'delivered')->update(['status' => 'completed']);
            
            DB::statement("PRAGMA foreign_keys = OFF");
            
            DB::statement("CREATE TABLE orders_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                reference VARCHAR NOT NULL,
                user_id INTEGER NOT NULL,
                printer_id INTEGER DEFAULT NULL,
                service_type VARCHAR CHECK(service_type IN ('print', 'photocopy', 'scan')) NOT NULL,
                format VARCHAR DEFAULT 'A4' CHECK(format IN ('A4', 'A3', 'A5', 'letter')) NOT NULL,
                color_mode VARCHAR DEFAULT 'bw' CHECK(color_mode IN ('color', 'bw')) NOT NULL,
                quantity INTEGER UNSIGNED DEFAULT 1 NOT NULL,
                pages INTEGER UNSIGNED DEFAULT 1 NOT NULL,
                unit_price DECIMAL(10, 2) NOT NULL,
                total_price DECIMAL(10, 2) NOT NULL,
                status VARCHAR DEFAULT 'pending' CHECK(status IN ('pending', 'accepted', 'in_progress', 'completed', 'rejected', 'cancelled')) NOT NULL,
                payment_method VARCHAR DEFAULT 'on_delivery' CHECK(payment_method IN ('online', 'on_delivery')) NOT NULL,
                payment_status VARCHAR DEFAULT 'unpaid' CHECK(payment_status IN ('unpaid', 'pending', 'paid', 'refunded')) NOT NULL,
                file_path VARCHAR DEFAULT NULL,
                file_name VARCHAR DEFAULT NULL,
                file_type VARCHAR DEFAULT NULL,
                file_size INTEGER UNSIGNED DEFAULT NULL,
                notes TEXT DEFAULT NULL,
                rejection_reason TEXT DEFAULT NULL,
                completed_at DATETIME DEFAULT NULL,
                created_at DATETIME DEFAULT NULL,
                updated_at DATETIME DEFAULT NULL,
                accepted_at DATETIME DEFAULT NULL
            )");
            
            DB::statement("INSERT INTO orders_new SELECT * FROM orders");
            
            DB::statement("DROP TABLE orders");
            
            DB::statement("ALTER TABLE orders_new RENAME TO orders");
            
            DB::statement("CREATE INDEX orders_status_created_at_index ON orders (status, created_at)");
            DB::statement("CREATE INDEX orders_user_id_status_index ON orders (user_id, status)");
            DB::statement("CREATE INDEX orders_printer_id_status_index ON orders (printer_id, status)");
            DB::statement("CREATE INDEX orders_status_printer_id_index ON orders (status, printer_id)");
            DB::statement("CREATE UNIQUE INDEX orders_reference_unique ON orders (reference)");
            
            DB::statement("PRAGMA foreign_keys = ON");
        }
    }
};
