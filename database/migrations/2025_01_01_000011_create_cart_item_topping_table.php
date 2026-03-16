<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_item_topping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topping_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 12, 0);

            $table->index('cart_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_item_topping');
    }
};
