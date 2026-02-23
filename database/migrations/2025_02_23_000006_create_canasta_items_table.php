<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('basket_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('basket_id')->constrained()->onDelete('cascade');
            $table->string('product_type'); // iphone, smart_watch, android, audifono
            $table->unsignedBigInteger('product_id');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->index(['product_type', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('basket_items');
    }
};
