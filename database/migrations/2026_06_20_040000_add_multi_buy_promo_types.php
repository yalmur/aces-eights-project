<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add 'buy_one_get_one' and 'multi_buy' promotion types.
     *
     * SQLite (test env) doesn't enforce enum constraints, so this only
     * needs to run on MySQL/MariaDB production databases.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE promotions MODIFY COLUMN type ENUM('percentage','fixed_amount','free_delivery','buy_one_get_one','multi_buy') NOT NULL DEFAULT 'percentage'");
        }
        // SQLite: no action needed — string column with CHECK constraint
        // already accepts any value, and controller validation gates input.
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE promotions MODIFY COLUMN type ENUM('percentage','fixed_amount','free_delivery') NOT NULL DEFAULT 'percentage'");
        }
    }
};
