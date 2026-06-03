<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('qty')->default(1);
            $table->decimal('unit_price', 8, 2);
            $table->string('size')->nullable();
            $table->string('crust')->nullable();
            $table->decimal('size_extra', 5, 2)->default(0);
            $table->decimal('crust_extra', 5, 2)->default(0);
            $table->json('added_toppings')->nullable();
            $table->json('removed_ingredients')->nullable();
            $table->string('instructions')->nullable();
            $table->decimal('line_total', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
