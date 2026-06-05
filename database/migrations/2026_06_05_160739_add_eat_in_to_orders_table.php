<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite (used in tests) doesn't support ALTER COLUMN on enums.
        // Use a raw query for MySQL; tests use RefreshDatabase so the base
        // migration runs fresh with the updated enum below.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN type ENUM('delivery','collection','eat_in') NOT NULL DEFAULT 'delivery'");
            DB::statement("ALTER TABLE orders MODIFY COLUMN customer_email VARCHAR(255) NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN type ENUM('delivery','collection') NOT NULL DEFAULT 'delivery'");
            DB::statement("ALTER TABLE orders MODIFY COLUMN customer_email VARCHAR(255) NOT NULL");
        }
    }
};
