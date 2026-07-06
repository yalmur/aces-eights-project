<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_slot_category', function (Blueprint $table) {
            $table->foreignId('deal_slot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['deal_slot_id', 'category_id']);
        });

        Schema::create('deal_slot_menu_item', function (Blueprint $table) {
            $table->foreignId('deal_slot_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->primary(['deal_slot_id', 'menu_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_slot_menu_item');
        Schema::dropIfExists('deal_slot_category');
    }
};
