<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('deal_id')->nullable()->after('menu_item_id')->constrained()->nullOnDelete();
            $table->json('deal_selections')->nullable()->after('deal_id');
            // Make menu_item_id nullable to support deal-only order items
            $table->foreignId('menu_item_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('deal_id');
            $table->dropColumn('deal_selections');
            $table->foreignId('menu_item_id')->nullable(false)->change();
        });
    }
};
