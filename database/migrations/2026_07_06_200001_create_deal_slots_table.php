<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            $table->string('label');              // "Main", "Side", "Drink"
            $table->unsignedTinyInteger('min_qty')->default(1);
            $table->unsignedTinyInteger('max_qty')->default(1);
            $table->boolean('is_required')->default(true);
            $table->boolean('is_free')->default(false); // BOGO "get" slot
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_slots');
    }
};
