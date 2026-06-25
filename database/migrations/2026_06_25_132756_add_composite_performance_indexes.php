<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // availableItems() relation: WHERE category_id = ? AND is_available = 1
        Schema::table('menu_items', function (Blueprint $table) {
            $table->index(['category_id', 'is_available'], 'menu_items_category_available_idx');
            $table->index(['is_featured', 'is_available'],  'menu_items_featured_available_idx');
        });

        // defaultAddress() at checkout: WHERE user_id = ? AND is_default = 1
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->index(['user_id', 'is_default'], 'user_addresses_user_default_idx');
        });

        // Account page paginated orders: WHERE user_id = ? ORDER BY created_at DESC
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'orders_user_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex('menu_items_category_available_idx');
            $table->dropIndex('menu_items_featured_available_idx');
        });

        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropIndex('user_addresses_user_default_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_created_idx');
        });
    }
};
